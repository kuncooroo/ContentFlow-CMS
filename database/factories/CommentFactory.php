<?php

namespace Database\Factories;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => Post::factory()->published(),
            'moderated_by_user_id' => null,
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'content' => fake()->paragraph(),
            'status' => CommentStatus::Pending,
            'moderated_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => CommentStatus::Approved,
            'moderated_at' => now(),
        ]);
    }

    public function spam(): static
    {
        return $this->state(fn (): array => [
            'status' => CommentStatus::Spam,
            'moderated_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'status' => CommentStatus::Rejected,
            'moderated_at' => now(),
        ]);
    }
}
