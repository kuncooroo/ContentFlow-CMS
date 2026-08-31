<?php

namespace App\Support\Comments;

use App\Services\Settings\SiteSettings;

class CommentsGate
{
    public static function enabled(): bool
    {
        return app(SiteSettings::class)->get()->comments_enabled;
    }
}
