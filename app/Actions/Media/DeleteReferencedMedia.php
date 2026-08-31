<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use App\Support\Audit\ActivityEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class DeleteReferencedMedia
{
    public function __construct(
        private readonly ActivityLogger $activityLogger,
    ) {}

    public function handle(Media $media, User $actor): void
    {
        if ($media->isReferenced()) {
            throw ValidationException::withMessages([
                'media' => 'This media item cannot be deleted because it is still referenced elsewhere.',
            ]);
        }

        $disk = $media->disk;
        $path = $media->path;

        DB::transaction(function () use ($media, $actor, $disk, $path): void {
            $this->activityLogger->record(
                $actor,
                ActivityEvent::MediaDeleted,
                $media,
                [
                    'original_name' => $media->original_name,
                    'path' => $media->path,
                ],
            );

            $media->delete();

            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        });
    }
}
