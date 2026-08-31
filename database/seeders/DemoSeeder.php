<?php

namespace Database\Seeders;

use App\Enums\CommentStatus;
use App\Enums\MenuItemType;
use App\Enums\PageStatus;
use App\Enums\PostStatus;
use App\Enums\UserStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Post;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'DemoPass123!';

    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            SiteSettingsSeeder::class,
            MenuSeeder::class,
        ]);

        SiteSetting::bootstrap()->update([
            'site_name' => 'ContentFlow CMS Demo',
            'site_description' => 'Explore posts, pages, media, and admin tools in a safe demo environment.',
            'timezone' => 'UTC',
            'locale' => 'en',
            'comments_enabled' => true,
        ]);

        $superAdmin = $this->seedUser(
            'Demo Super Admin',
            'demo-superadmin@contentflow.test',
            RoleName::SuperAdmin,
        );

        $admin = $this->seedUser(
            'Demo Administrator',
            'demo-admin@contentflow.test',
            RoleName::Administrator,
        );

        $editor = $this->seedUser(
            'Demo Editor',
            'demo-editor@contentflow.test',
            RoleName::Editor,
        );

        $news = Category::query()->updateOrCreate(
            ['slug' => 'news'],
            ['name' => 'News', 'description' => 'Latest updates and announcements.'],
        );

        $guides = Category::query()->updateOrCreate(
            ['slug' => 'guides'],
            ['name' => 'Guides', 'description' => 'How-to articles and tutorials.'],
        );

        $cmsTag = Tag::query()->updateOrCreate(
            ['slug' => 'cms'],
            ['name' => 'CMS'],
        );

        $demoTag = Tag::query()->updateOrCreate(
            ['slug' => 'demo'],
            ['name' => 'Demo'],
        );

        $welcomePost = Post::query()->updateOrCreate(
            ['slug' => 'welcome-to-contentflow'],
            [
                'author_id' => $editor->id,
                'title' => 'Welcome to ContentFlow',
                'excerpt' => 'A quick tour of the demo CMS.',
                'content' => '<p>This demo site includes published posts, pages, categories, tags, and comments.</p>',
                'status' => PostStatus::Published,
                'publish_at' => now()->subDay(),
                'robots_index' => true,
            ],
        );

        $welcomePost->categories()->sync([$news->id]);
        $welcomePost->tags()->sync([$cmsTag->id, $demoTag->id]);

        Post::query()->updateOrCreate(
            ['slug' => 'editorial-workflow-overview'],
            [
                'author_id' => $editor->id,
                'title' => 'Editorial Workflow Overview',
                'excerpt' => 'Draft, schedule, publish, and archive content.',
                'content' => '<p>Editors can manage posts with statuses and scheduled publishing.</p>',
                'status' => PostStatus::Published,
                'publish_at' => now()->subHours(6),
                'robots_index' => true,
            ],
        )->categories()->sync([$guides->id]);

        Post::query()->updateOrCreate(
            ['slug' => 'scheduled-post-preview'],
            [
                'author_id' => $admin->id,
                'title' => 'Scheduled Post Preview',
                'excerpt' => 'This post is scheduled for a future date.',
                'content' => '<p>Scheduled posts publish automatically when their time arrives.</p>',
                'status' => PostStatus::Scheduled,
                'publish_at' => now()->addWeek(),
                'robots_index' => false,
            ],
        );

        $aboutPage = Page::query()->updateOrCreate(
            ['slug' => 'about'],
            [
                'author_id' => $admin->id,
                'title' => 'About This Demo',
                'content' => '<p>ContentFlow CMS demo environment for prospects and evaluators.</p>',
                'status' => PageStatus::Published,
                'publish_at' => now()->subDay(),
                'robots_index' => true,
            ],
        );

        Page::query()->updateOrCreate(
            ['slug' => 'contact'],
            [
                'author_id' => $admin->id,
                'title' => 'Contact',
                'content' => '<p>Reach out through your sales contact for a full evaluation.</p>',
                'status' => PageStatus::Published,
                'publish_at' => now()->subDay(),
                'robots_index' => true,
            ],
        );

        Comment::query()->updateOrCreate(
            [
                'post_id' => $welcomePost->id,
                'author_email' => 'visitor@example.com',
            ],
            [
                'author_name' => 'Demo Visitor',
                'content' => 'Great demo — the admin area is easy to navigate.',
                'status' => CommentStatus::Approved,
                'moderated_by_user_id' => $editor->id,
                'moderated_at' => now(),
            ],
        );

        Comment::query()->updateOrCreate(
            [
                'post_id' => $welcomePost->id,
                'author_email' => 'pending@example.com',
            ],
            [
                'author_name' => 'Pending Review',
                'content' => 'This comment awaits moderation in the admin queue.',
                'status' => CommentStatus::Pending,
            ],
        );

        $primaryMenu = Menu::query()->where('key', 'primary')->firstOrFail();

        MenuItem::query()->updateOrCreate(
            ['menu_id' => $primaryMenu->id, 'label' => 'Home'],
            [
                'type' => MenuItemType::Custom,
                'custom_url' => '/',
                'position' => 0,
            ],
        );

        MenuItem::query()->updateOrCreate(
            ['menu_id' => $primaryMenu->id, 'label' => 'About'],
            [
                'type' => MenuItemType::Page,
                'page_id' => $aboutPage->id,
                'position' => 1,
            ],
        );

        MenuItem::query()->updateOrCreate(
            ['menu_id' => $primaryMenu->id, 'label' => 'News'],
            [
                'type' => MenuItemType::Category,
                'category_id' => $news->id,
                'position' => 2,
            ],
        );
    }

    private function seedUser(string $name, string $email, string $roleName): User
    {
        $role = Role::query()->where('name', $roleName)->firstOrFail();

        $user = User::query()->updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make(self::DEMO_PASSWORD),
                'status' => UserStatus::Active,
            ],
        );

        $user->roles()->sync([
            $role->id => [
                'assigned_by_user_id' => null,
                'created_at' => now(),
            ],
        ]);

        return $user;
    }
}
