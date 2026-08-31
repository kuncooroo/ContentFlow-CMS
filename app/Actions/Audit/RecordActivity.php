<?php

namespace App\Actions\Audit;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\Audit\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class RecordActivity
{
    public function __construct(
        private readonly ActivityLogger $logger,
    ) {}

    /**
     * @param  array<string, mixed>  $properties
     */
    public function handle(
        ?User $actor,
        string $event,
        ?Model $subject = null,
        array $properties = [],
    ): ActivityLog {
        return $this->logger->record($actor, $event, $subject, $properties);
    }
}
