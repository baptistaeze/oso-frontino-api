<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class SecureImageValidator
{
    private const MAGIC_BYTES = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png' => ["\x89\x50\x4E\x47\x0D\x0A\x1A\x0A"],
        'image/gif' => ["GIF87a", "GIF89a"],
    ];

    private const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/gif'];

    /**
     * Validates image: magic bytes + re-encodes to strip malicious content.
     * @throws ValidationException if file is invalid or potentially malicious
     */
    public function validateAndSanitize(UploadedFile $file): void
    {
        $mime = $file->getMimeType();

        if (! in_array($mime, self::ALLOWED_MIMES)) {
            throw ValidationException::withMessages([
                'image' => ['El archivo debe ser JPG, PNG o GIF únicamente.'],
            ]);
        }

        $content = file_get_contents($file->getRealPath());
        if (! $this->magicBytesMatch($content, $mime)) {
            throw ValidationException::withMessages([
                'image' => ['El contenido del archivo no coincide con su extensión. Archivo rechazado por seguridad.'],
            ]);
        }

        if ($this->containsSuspiciousContent($content)) {
            throw ValidationException::withMessages([
                'image' => ['El archivo contiene contenido sospechoso y ha sido rechazado por seguridad.'],
            ]);
        }

        $this->validateImageIsReadable($file);
    }

    /**
     * Re-encodes image with GD to strip any embedded malicious data.
     * Returns temp path to the clean image, or null if re-encoding failed.
     */
    public function reEncodeToCleanFile(UploadedFile $file): ?string
    {
        $mime = $file->getMimeType();
        $path = $file->getRealPath();

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            default => null,
        };

        if ($image === false || $image === null) {
            throw ValidationException::withMessages([
                'image' => ['No se pudo procesar la imagen. El archivo puede estar corrupto o contener datos no válidos.'],
            ]);
        }

        $tempPath = sys_get_temp_dir() . '/' . uniqid('img_') . '.' . $this->getExtension($mime);

        try {
            $success = match ($mime) {
                'image/jpeg' => imagejpeg($image, $tempPath, 90),
                'image/png' => imagepng($image, $tempPath, 9),
                'image/gif' => imagegif($image, $tempPath),
                default => false,
            };

            imagedestroy($image);

            return $success ? $tempPath : null;
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

    /**
     * Scans for embedded PHP/code, script tags, or executable patterns.
     */
    private function containsSuspiciousContent(string $content): bool
    {
        $dangerousPatterns = [
            '<?php',
            '<?=',
            '<script',
            'javascript:',
            'vbscript:',
            'data:text/html',
            'data:application/javascript',
            "\x00", // null byte
        ];

        $lower = strtolower($content);

        foreach ($dangerousPatterns as $pattern) {
            if (stripos($lower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    private function validateImageIsReadable(UploadedFile $file): void
    {
        $mime = $file->getMimeType();
        $resource = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($file->getRealPath()),
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
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            default => 'jpg',
        };
    }
}
