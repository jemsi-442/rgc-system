<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class SafeImageUpload
{
    /**
     * @return array{path:string,name:string,mime_type:string}
     */
    public static function storePublicImage(UploadedFile $file, string $directory, ?string $displayName = null): array
    {
        if (! $file->isValid()) {
            throw self::validationFailure();
        }

        $contents = @file_get_contents($file->getRealPath());

        if ($contents === false) {
            throw self::validationFailure();
        }

        $image = @imagecreatefromstring($contents);

        if ($image === false) {
            throw self::validationFailure();
        }

        $mimeType = self::targetMimeType($file);
        $extension = $mimeType === 'image/png' ? 'png' : 'jpg';
        $path = trim($directory, '/') . '/' . Str::uuid() . '.' . $extension;

        try {
            $binary = self::encodeImage($image, $mimeType);
            Storage::disk('public')->put($path, $binary);
        } catch (Throwable $exception) {
            report($exception);
            throw self::validationFailure();
        } finally {
            imagedestroy($image);
        }

        return [
            'path' => $path,
            'name' => self::safeDisplayName($displayName ?? $file->getClientOriginalName(), $extension),
            'mime_type' => $mimeType,
        ];
    }

    private static function validationFailure(): ValidationException
    {
        return ValidationException::withMessages([
            'image' => __('Image upload failed. Please try again with another image.'),
        ]);
    }

    private static function targetMimeType(UploadedFile $file): string
    {
        $mimeType = $file->getMimeType() ?: 'application/octet-stream';

        return in_array($mimeType, ['image/png', 'image/webp', 'image/gif'], true)
            ? 'image/png'
            : 'image/jpeg';
    }

    private static function encodeImage(\GdImage $image, string $mimeType): string
    {
        ob_start();

        if ($mimeType === 'image/png') {
            imagealphablending($image, false);
            imagesavealpha($image, true);
            imagepng($image, null, 6);
        } else {
            $background = imagecreatetruecolor(imagesx($image), imagesy($image));
            $white = imagecolorallocate($background, 255, 255, 255);
            imagefill($background, 0, 0, $white);
            imagecopy($background, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            imagejpeg($background, null, 86);
            imagedestroy($background);
        }

        $binary = ob_get_clean();

        if (! is_string($binary) || $binary === '') {
            throw new \RuntimeException('Image encoding failed.');
        }

        return $binary;
    }

    private static function safeDisplayName(string $originalName, string $extension): string
    {
        $name = trim(basename($originalName));
        $name = preg_replace('/[\r\n\t]+/', ' ', $name) ?? $name;
        $base = pathinfo($name, PATHINFO_FILENAME);
        $base = Str::limit(trim((string) $base), 180, '');
        $base = $base !== '' ? $base : 'image';

        return $base . '.' . $extension;
    }
}
