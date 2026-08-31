<?php

namespace App\Models;

use App\Policies\ActivityLogPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(ActivityLogPolicy::class)]
class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'actor_user_id',
        'event',
        'subject_type',
        'subject_id',
        'properties',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(static fn () => false);
        static::deleting(static fn () => false);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function actorLabel(): string
    {
        return $this->actor?->name ?? 'Deleted user';
    }
}
