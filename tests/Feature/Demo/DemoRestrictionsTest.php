<?php

namespace Tests\Feature\Demo;

use App\Actions\AccessControl\AssignRole;
use App\Actions\AccessControl\SyncRolePermissions;
use App\Actions\Posts\UpdatePost;
use App\Actions\Settings\UpdateSiteSettings;
use App\Actions\Users\ChangeUserStatus;
use App\Actions\Users\UpdateUser;
use App\Enums\UserStatus;
use App\Models\Post;
use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\AccessControl\RoleName;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DemoRestrictionsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('demo.enabled', true);
    }

    protected function tearDown(): void
    {
        Config::set('demo.enabled', false);

        parent::tearDown();
    }

    public function test_cannot_deactivate_demo_user(): void
    {
        $this->seed(DemoSeeder::class);

        $demoUser = User::query()->where('email', 'demo-admin@contentflow.test')->firstOrFail();
        $actor = User::factory()->superAdmin()->create();

        $this->expectException(ValidationException::class);

        app(ChangeUserStatus::class)->handle(
            $demoUser,
            UserStatus::Inactive,
            $actor,
        );
    }

    public function test_cannot_change_demo_user_password(): void
    {
        $this->seed(DemoSeeder::class);

        $demoUser = User::query()->where('email', 'demo-editor@contentflow.test')->firstOrFail();

        $this->expectException(ValidationException::class);

        app(UpdateUser::class)->handle($demoUser, [
            'name' => $demoUser->name,
            'email' => $demoUser->email,
            'password' => 'NewPassword123!',
        ]);
    }

    public function test_cannot_change_demo_user_roles(): void
    {
        $this->seed(DemoSeeder::class);

        $demoUser = User::query()->where('email', 'demo-editor@contentflow.test')->firstOrFail();
        $authorRole = Role::query()->where('name', RoleName::Author)->firstOrFail();

        $this->expectException(ValidationException::class);

        app(AssignRole::class)->handle(
            $demoUser,
            [$authorRole->id],
            User::factory()->superAdmin()->create(),
        );
    }

    public function test_cannot_sync_system_role_permissions_in_demo(): void
    {
        $role = Role::query()->where('name', RoleName::Editor)->firstOrFail();
        $permissionIds = $role->permissions()->pluck('permissions.id')->take(1)->all();
        $actor = User::factory()->superAdmin()->create();

        $this->expectException(ValidationException::class);

        app(SyncRolePermissions::class)->handle($role, $permissionIds, $actor);
    }

    public function test_cannot_update_site_settings_in_demo(): void
    {
        $settings = SiteSetting::bootstrap();
        $actor = User::factory()->superAdmin()->create();

        $this->expectException(ValidationException::class);

        app(UpdateSiteSettings::class)->handle(
            settings: $settings,
            actor: $actor,
            siteName: 'Changed Name',
            siteDescription: null,
            contactEmail: null,
            contactPhone: null,
            contactAddress: null,
            socialLinks: [],
            defaultSeoTitle: null,
            defaultMetaDescription: null,
            defaultRobotsIndex: true,
            logoMediaId: null,
            faviconMediaId: null,
            defaultOgMediaId: null,
            timezone: 'UTC',
            locale: 'en',
            commentsEnabled: true,
        );
    }

    public function test_editorial_post_updates_are_allowed_in_demo(): void
    {
        $this->seed(DemoSeeder::class);

        $post = Post::query()->where('slug', 'welcome-to-contentflow')->firstOrFail();

        $updated = app(UpdatePost::class)->handle(
            post: $post,
            title: 'Updated Demo Title',
            slug: $post->slug,
            content: $post->content,
            excerpt: $post->excerpt,
            featuredMediaId: null,
            categoryIds: $post->categories()->pluck('categories.id')->all(),
            tagIds: $post->tags()->pluck('tags.id')->all(),
        );

        $this->assertSame('Updated Demo Title', $updated->title);
    }

    public function test_restrictions_are_inert_when_demo_mode_is_off(): void
    {
        Config::set('demo.enabled', false);

        $settings = SiteSetting::bootstrap();
        $actor = User::factory()->superAdmin()->create();

        $updated = app(UpdateSiteSettings::class)->handle(
            settings: $settings,
            actor: $actor,
            siteName: 'Non Demo Name',
            siteDescription: null,
            contactEmail: null,
            contactPhone: null,
            contactAddress: null,
            socialLinks: [],
            defaultSeoTitle: null,
            defaultMetaDescription: null,
            defaultRobotsIndex: true,
            logoMediaId: null,
            faviconMediaId: null,
            defaultOgMediaId: null,
            timezone: 'UTC',
            locale: 'en',
            commentsEnabled: true,
        );

        $this->assertSame('Non Demo Name', $updated->site_name);
    }
}
