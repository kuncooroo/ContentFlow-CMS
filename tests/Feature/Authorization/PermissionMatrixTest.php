<?php

namespace Tests\Feature\Authorization;

use App\Models\Post;
use App\Models\User;
use App\Policies\PostPolicy;
use App\Support\Permissions\PermissionNames;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PermissionMatrixTest extends TestCase
{
    use DatabaseTransactions;

    public function test_author_cannot_publish_posts_by_default(): void
    {
        $author = User::factory()->author()->create();

        $this->assertFalse($author->hasPermission(PermissionNames::PostsPublish));
        $this->assertFalse($author->hasPermission(PermissionNames::PostsSchedule));
        $this->assertTrue($author->hasPermission(PermissionNames::PostsCreate));

        $policy = app(PostPolicy::class);
        $post = Post::factory()->make(['author_id' => $author->id]);

        $this->assertFalse($policy->publish($author, $post));
        $this->assertFalse($policy->schedule($author, $post));
    }

    public function test_editor_can_publish_posts(): void
    {
        $editor = User::factory()->editor()->create();

        $this->assertTrue($editor->hasPermission(PermissionNames::PostsPublish));

        $policy = app(PostPolicy::class);
        $post = Post::factory()->make(['author_id' => $editor->id]);

        $this->assertTrue($policy->publish($editor, $post));
    }

    public function test_super_admin_has_all_permissions(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        foreach (PermissionNames::all() as $permission) {
            $this->assertTrue($superAdmin->hasPermission($permission), "Missing permission: {$permission}");
        }
    }

    public function test_administrator_can_manage_users_and_roles(): void
    {
        $admin = User::factory()->administrator()->create();

        $this->assertTrue($admin->hasPermission(PermissionNames::UsersManage));
        $this->assertTrue($admin->hasPermission(PermissionNames::RolesManage));
    }
}
