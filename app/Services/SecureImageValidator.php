<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class SecureImageValidator
{
    private const MAX_SIZE = 600;

    private const MAX_FILE_BYTES = 2 * 1024 * 1024; // 2MB

    private const MAGIC_BYTES = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'image/gif' => ["GIF87a", "GIF89a"],
    ];

    private const ALLOWED_MIMES = ['image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'];

    /**
     * Validates image: magic bytes + re-encodes to strip malicious content.
     * @throws ValidationException if file is invalid or potentially malicious
     */
    public function validateAndSanitize(UploadedFile $file): void
    {
        $rawMime = $file->getMimeType();
        if (! in_array($rawMime, self::ALLOWED_MIMES)) {
            throw ValidationException::withMessages([
                'image' => ['El archivo debe ser JPG, PNG o GIF únicamente.'],
            ]);
        }

        $content = file_get_contents($file->getRealPath());
        $mime = $this->normalizeMime($rawMime);
        if (! $this->magicBytesMatch($content, $mime)) {
            throw ValidationException::withMessages([
                'image' => ['El contenido del archivo no coincide con su extensión. Archivo rechazado por seguridad.'],
            ]);
        }

        // No escaneamos el binario crudo: las imágenes comprimidas pueden contener secuencias
        // que coinciden con "<?php", "script", etc. por azar → falsos positivos. La sanitización
        // real ocurre al re-codificar con GD: decodificamos y guardamos solo píxeles.
        $this->validateImageIsReadable($file);
    }

    /**
     * Re-encodes image with GD: resizes to max 600x600, compresses to max 2MB.
     * Returns temp path to the clean image.
     */
    public function reEncodeToCleanFile(UploadedFile $file): ?string
    {
        $mime = $this->normalizeMime($file->getMimeType());
        $path = $file->getRealPath();

        $image = match ($mime) {
            'image/jpeg', 'image/pjpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            default => null,
        };

        if ($image === false || $image === null) {
            throw ValidationException::withMessages([
                'image' => ['No se pudo procesar la imagen. El archivo puede estar corrupto o contener datos no válidos.'],
            ]);
        }

        $resized = $this->resizeToFit($image, self::MAX_SIZE);
        if ($resized !== $image) {
            imagedestroy($image);
            $image = $resized;
        }

        $ext = $this->getExtension($mime);
        $tempPath = sys_get_temp_dir() . '/' . uniqid('img_') . '.' . $ext;

        try {
            $finalPath = $this->saveUnderMaxSize($image, $tempPath, $mime, $ext);
            imagedestroy($image);

            return $finalPath;
        } catch (\Throwable $e) {
            if (isset($image) && ($image instanceof \GdImage || is_resource($image))) {
                @imagedestroy($image);
            }
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
            throw ValidationException::withMessages([
                'image' => ['Error al procesar la imagen. Por favor intente con otro archivo.'],
            ]);
        }
    }

    private function resizeToFit(\GdImage $image, int $maxSize): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);

        if ($width <= $maxSize && $height <= $maxSize) {
            return $image;
        }

        $ratio = min($maxSize / $width, $maxSize / $height);
        $newWidth = (int) round($width * $ratio);
        $newHeight = (int) round($height * $ratio);

        $resized = imagecreatetruecolor($newWidth, $newHeight);
        if ($resized === false) {
            return $image;
        }

        imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        return $resized;
    }

    private function saveUnderMaxSize(\GdImage $image, string $tempPath, string $mime, string $ext): string
    {
        if ($mime === 'image/gif') {
            $image = $this->prepareForJpeg($image);
            $mime = 'image/jpeg';
            $tempPath = preg_replace('/\.gif$/', '.jpg', $tempPath) ?: $tempPath;
        }

        if ($mime === 'image/png') {
            $quality = 9;
            for ($i = 0; $i < 5; $i++) {
                if (imagepng($image, $tempPath, $quality) && $this->isUnderLimit($tempPath)) {
                    return $tempPath;
                }
                $quality = max(0, $quality - 2);
            }
            @unlink($tempPath);
            $image = $this->prepareForJpeg($image);
            $mime = 'image/jpeg';
            $tempPath = preg_replace('/\.png$/', '.jpg', $tempPath) ?: $tempPath;
        }

        $quality = 90;
        for ($i = 0; $i < 8; $i++) {
            if (imagejpeg($image, $tempPath, $quality) && $this->isUnderLimit($tempPath)) {
                return $tempPath;
            }
            $quality = max(40, $quality - 10);
        }

        throw ValidationException::withMessages([
            'image' => ['No se pudo reducir la imagen a menos de 2MB. Intente con una imagen más pequeña.'],
        ]);
    }

    private function isUnderLimit(string $path): bool
    {
        $size = @filesize($path);

        return $size !== false && $size <= self::MAX_FILE_BYTES;
    }

    private function prepareForJpeg(\GdImage $image): \GdImage
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $out = imagecreatetruecolor($w, $h);
        if ($out === false) {
            throw ValidationException::withMessages(['image' => ['Error al procesar la imagen.']]);
        }
        $white = imagecolorallocate($out, 255, 255, 255);
        imagefill($out, 0, 0, $white);
        if (imageistruecolor($image)) {
            imagealphablending($out, true);
        }
        imagecopy($out, $image, 0, 0, 0, 0, $w, $h);
        imagedestroy($image);

        return $out;
    }


    private function magicBytesMatch(string $content, string $mime): bool
    {
        $expected = self::MAGIC_BYTES[$mime] ?? null;
        if (! $expected) {
            return false;
        }

        foreach ($expected as $bytes) {
            if (str_starts_with($content, $bytes)) {
                return true;
            }
        }

        return false;
    }

    private function validateImageIsReadable(UploadedFile $file): void
    {
        $mime = $file->getMimeType();
        $resource = match ($mime) {
            'image/jpeg', 'image/pjpeg' => @imagecreatefromjpeg($file->getRealPath()),
            'image/png' => @imagecreatefrompng($file->getRealPath()),
            'image/gif' => @imagecreatefromgif($file->getRealPath()),
            default => null,
        };

        if ($resource === false || $resource === null) {
            throw ValidationException::withMessages([
                'image' => ['La imagen no pudo ser procesada. Verifique que sea un archivo JPG, PNG o GIF válido.'],
            ]);
        }

        imagedestroy($resource);
    }

    private function getExtension(string $mime): string
    {
        return match ($mime) {
            'image/jpeg', 'image/pjpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }

    private function normalizeMime(?string $mime): string
    {
        return match ($mime) {
            'image/pjpeg' => 'image/jpeg',
            default => $mime ?? 'image/jpeg',
        };
    }
}
