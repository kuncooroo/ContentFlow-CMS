<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('featured_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('status', 20)->default('draft');
            $table->dateTime('publish_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->boolean('robots_index')->default(true);
            $table->timestamps();

            $table->index(['status', 'publish_at']);
            $table->index(['author_id', 'status']);
            $table->index(['status', 'created_at']);
        });

        Schema::create('post_category', function (Blueprint $table): void {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();

            $table->primary(['post_id', 'category_id']);
            $table->index(['category_id', 'post_id']);
        });

        Schema::create('post_tag', function (Blueprint $table): void {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();

            $table->primary(['post_id', 'tag_id']);
            $table->index(['tag_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('post_category');
        Schema::dropIfExists('posts');
    }
};
