<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ProductImageService
{
    private const DISK = 'public';

    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
    ];

    public function __construct(
        private SecureImageValidator $validator
    ) {}

    /**
     * Store uploaded image and return the path to save in DB.
     * Validates (magic bytes, malicious content) and re-encodes to strip embedded scripts.
     * Path format: storage/products/{type}/{uuid}.{ext}
     */
    public function store(UploadedFile $file, string $productType): string
    {
        $this->validator->validateAndSanitize($file);

        $tempPath = $this->validator->reEncodeToCleanFile($file);

        try {
            $mime = $file->getMimeType();
            $ext = self::MIME_EXTENSIONS[$mime] ?? 'jpg';
            $filename = Str::uuid() . '.' . $ext;
            $directory = "products/{$productType}";

            $storedPath = \Illuminate\Support\Facades\Storage::disk(self::DISK)
                ->putFileAs($directory, new \Illuminate\Http\File($tempPath), $filename);

            return 'storage/' . $storedPath;
        } finally {
            if ($tempPath && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Delete image file from storage if it belongs to our products folder.
     */
    public function delete(?string $imagePath): void
    {
        if (! $imagePath || ! str_starts_with($imagePath, 'storage/products/')) {
            return;
        }

        $relativePath = str_replace('storage/', '', $imagePath);
        \Illuminate\Support\Facades\Storage::disk(self::DISK)->delete($relativePath);
    }
}
