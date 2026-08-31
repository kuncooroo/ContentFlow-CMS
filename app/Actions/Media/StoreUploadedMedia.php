<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Models\User;
use App\Support\Media\AllowedMediaTypes;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoreUploadedMedia
{
    public function handle(UploadedFile $file, User $uploader): Media
    {
        $mimeType = $this->assertValidUpload($file);

        $disk = AllowedMediaTypes::disk();
        $extension = strtolower($file->getClientOriginalExtension());
        $storedFileName = Str::uuid().'.'.$extension;
        $directory = 'media/'.now()->format('Y/m');
        $path = $directory.'/'.$storedFileName;

        if (Storage::disk($disk)->exists($path)) {
            throw ValidationException::withMessages([
                'upload' => 'A file already exists at the generated storage path.',
            ]);
        }

        $storedPath = null;

        try {
            return DB::transaction(function () use ($file, $uploader, $disk, $path, $storedFileName, $mimeType, &$storedPath): Media {
                $storedPath = $file->storeAs(
                    dirname($path),
                    basename($path),
                    ['disk' => $disk],
                );

                if ($storedPath === false) {
                    throw ValidationException::withMessages([
                        'upload' => 'The file could not be stored.',
                    ]);
                }

                [$width, $height] = $this->resolveDimensions($file);

                return Media::query()->create([
                    'uploaded_by_user_id' => $uploader->id,
                    'disk' => $disk,
                    'path' => $storedPath,
                    'original_name' => $file->getClientOriginalName(),
                    'file_name' => $storedFileName,
                    'mime_type' => $mimeType,
                    'size_bytes' => $file->getSize(),
                    'width' => $width,
                    'height' => $height,
                ]);
            });
        } catch (\Throwable $exception) {
            if ($storedPath !== null && Storage::disk($disk)->exists($storedPath)) {
                Storage::disk($disk)->delete($storedPath);
            }

            throw $exception;
        }
    }

    private function assertValidUpload(UploadedFile $file): string
    {
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'upload' => 'The uploaded file is invalid.',
            ]);
        }

        if ($file->getSize() <= 0) {
            throw ValidationException::withMessages([
                'upload' => 'The uploaded file is empty.',
            ]);
        }

        if ($file->getSize() > AllowedMediaTypes::maxUploadBytes()) {
            throw ValidationException::withMessages([
                'upload' => 'The uploaded file exceeds the maximum allowed size.',
            ]);
        }

        $extension = strtolower($file->getClientOriginalExtension());

        if (AllowedMediaTypes::isBlockedFilename($file->getClientOriginalName())) {
            throw ValidationException::withMessages([
                'upload' => 'This file type is not allowed.',
            ]);
        }

        if (! AllowedMediaTypes::isAllowedExtension($extension)) {
            throw ValidationException::withMessages([
                'upload' => 'This file type is not allowed.',
            ]);
        }

        $detectedMime = $this->detectMimeType($file);

        if (! AllowedMediaTypes::isAllowedMime($detectedMime)) {
            throw ValidationException::withMessages([
                'upload' => 'This file type is not allowed.',
            ]);
        }

        if (@getimagesize($file->getRealPath()) === false) {
            throw ValidationException::withMessages([
                'upload' => 'The uploaded file is not a valid image.',
            ]);
        }

        return $detectedMime;
    }

    private function detectMimeType(UploadedFile $file): string
    {
        $path = $file->getRealPath();

        if ($path === false) {
            return '';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path);

        return is_string($mime) ? strtolower($mime) : '';
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function resolveDimensions(UploadedFile $file): array
    {
        $dimensions = @getimagesize($file->getRealPath());

        if ($dimensions === false) {
            return [null, null];
        }

        return [$dimensions[0], $dimensions[1]];
    }
}
