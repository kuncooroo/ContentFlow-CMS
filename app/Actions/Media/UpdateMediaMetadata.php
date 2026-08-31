<?php

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Support\Facades\DB;

class UpdateMediaMetadata
{
    public function handle(Media $media, ?string $altText): Media
    {
        return DB::transaction(function () use ($media, $altText): Media {
            $media->alt_text = $altText !== '' ? $altText : null;
            $media->save();

            return $media->fresh();
        });
    }
}
