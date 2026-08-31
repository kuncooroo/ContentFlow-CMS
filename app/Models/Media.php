<?php

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'uploaded_by_user_id',
        'disk',
        'path',
        'original_name',
        'file_name',
        'mime_type',
        'size_bytes',
        'width',
        'height',
        'alt_text',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    /**
     * @return HasMany<MediaReference, $this>
     */
    public function references(): HasMany
    {
        return $this->hasMany(MediaReference::class);
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function isReferenced(): bool
    {
        if ($this->references()->exists()) {
            return true;
        }

        if (Schema::hasTable('posts')) {
            $referencedByPost = DB::table('posts')
                ->where('featured_media_id', $this->id)
                ->orWhere('og_media_id', $this->id)
                ->exists();

            if ($referencedByPost) {
                return true;
            }
        }

        if (Schema::hasTable('pages')) {
            $referencedByPage = DB::table('pages')
                ->where('og_media_id', $this->id)
                ->exists();

            if ($referencedByPage) {
                return true;
            }
        }

        if (Schema::hasTable('site_settings')) {
            $referencedBySettings = DB::table('site_settings')
                ->where('logo_media_id', $this->id)
                ->orWhere('favicon_media_id', $this->id)
                ->orWhere('default_og_media_id', $this->id)
                ->exists();

            if ($referencedBySettings) {
                return true;
            }
        }

        return false;
    }
}
