<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('status', 20)->default('draft');
            $table->dateTime('publish_at')->nullable();
            $table->string('seo_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url', 2048)->nullable();
            $table->boolean('robots_index')->default(true);
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['author_id', 'status']);
            $table->index('og_media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
