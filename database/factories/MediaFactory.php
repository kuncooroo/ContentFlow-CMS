<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use App\Support\Media\AllowedMediaTypes;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $disk = AllowedMediaTypes::disk();
        $fileName = Str::uuid().'.jpg';
        $path = 'media/'.now()->format('Y/m').'/'.$fileName;

        return [
            'uploaded_by_user_id' => User::factory(),
            'disk' => $disk,
            'path' => $path,
            'original_name' => 'sample.jpg',
            'file_name' => $fileName,
            'mime_type' => 'image/jpeg',
            'size_bytes' => 1024,
            'width' => 800,
            'height' => 600,
            'alt_text' => fake()->optional()->sentence(3),
        ];
    }

    public function withStoredFile(): static
    {
        return $this->afterCreating(function (Media $media): void {
            Storage::disk($media->disk)->put($media->path, 'fake-image-content');
        });
    }
}
