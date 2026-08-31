<?php

namespace App\Models;

use App\Policies\SiteSettingPolicy;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(SiteSettingPolicy::class)]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    public const SINGLETON_ID = 1;

    public $incrementing = false;

    protected $keyType = 'int';

    protected $primaryKey = 'id';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'site_name',
        'site_description',
        'logo_media_id',
        'favicon_media_id',
        'contact_email',
        'contact_phone',
        'contact_address',
        'social_links',
        'default_seo_title',
        'default_meta_description',
        'default_og_media_id',
        'default_robots_index',
        'timezone',
        'locale',
        'comments_enabled',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'default_robots_index' => 'boolean',
            'comments_enabled' => 'boolean',
        ];
    }

    public static function bootstrap(): self
    {
        return static::query()->firstOrCreate(
            ['id' => self::SINGLETON_ID],
            [
                'site_name' => config('app.name', 'ContentFlow CMS'),
                'timezone' => config('app.timezone', 'UTC'),
                'locale' => config('app.locale', 'en'),
                'default_robots_index' => true,
                'comments_enabled' => true,
            ],
        );
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function logoMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'logo_media_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function faviconMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'favicon_media_id');
    }

    /**
     * @return BelongsTo<Media, $this>
     */
    public function defaultOgMedia(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'default_og_media_id');
    }
}
