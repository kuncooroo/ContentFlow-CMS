<?php

namespace App\Support\Demo;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\Settings\SiteSettings;
use Database\Seeders\DemoSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DemoReset
{
    public function __construct(
        private readonly SiteSettings $siteSettings,
    ) {}

    public function reset(): void
    {
        if (! DemoGuard::isEnabled()) {
            throw ValidationException::withMessages([
                'demo' => 'Demo reset is only available when DEMO_MODE is enabled.',
            ]);
        }

        DB::transaction(function (): void {
            DB::table('post_tag')->delete();
            DB::table('post_category')->delete();
            DB::table('media_references')->delete();

            Comment::query()->delete();
            MenuItem::query()->delete();
            Post::query()->delete();
            Page::query()->delete();
            Category::query()->delete();
            Tag::query()->delete();
            Media::query()->delete();
            ActivityLog::query()->delete();
            User::query()->delete();

            app(DemoSeeder::class)->run();
        });

        $this->siteSettings->invalidate();
    }
}
