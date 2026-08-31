<?php

namespace Tests\Feature\Smoke;

use App\Models\Post;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PublicHomeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_public_homepage_renders(): void
    {
        $post = Post::factory()->published()->create(['title' => 'Homepage Featured Post']);

        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('Latest posts')
            ->assertSee('Homepage Featured Post');
    }
}
