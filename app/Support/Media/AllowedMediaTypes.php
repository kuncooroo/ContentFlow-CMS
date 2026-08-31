<?php

namespace App\Support\Media;

class AllowedMediaTypes
{
    /**
     * @return list<string>
     */
    public static function mimes(): array
    {
        return config('media.allowed_mimes', []);
    }

    /**
     * @return list<string>
     */
    public static function extensions(): array
    {
        return config('media.allowed_extensions', []);
    }

    public static function maxUploadBytes(): int
    {
        return (int) config('media.max_upload_kilobytes', 5120) * 1024;
    }

    public static function disk(): string
    {
        return (string) config('media.disk', 'public');
    }

    public static function isAllowedMime(string $mime): bool
    {
        return in_array(strtolower($mime), self::mimes(), true);
    }

    public static function isAllowedExtension(string $extension): bool
    {
        $extension = strtolower($extension);

        if (self::isBlockedExtension($extension)) {
            return false;
        }

        return in_array($extension, self::extensions(), true);
    }

    public static function isBlockedExtension(string $extension): bool
    {
        return in_array(strtolower($extension), self::blockedExtensions(), true);
    }

    public static function isBlockedFilename(string $filename): bool
    {
        $lower = strtolower($filename);

        foreach (self::blockedExtensions() as $extension) {
            if (str_contains($lower, '.'.$extension)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<string>
     */
    public static function blockedExtensions(): array
    {
        return [
            'php',
            'phtml',
            'phar',
            'exe',
            'sh',
            'bat',
            'cmd',
            'js',
            'html',
            'htm',
            'svg',
            'xml',
            'htaccess',
        ];
    }
}
