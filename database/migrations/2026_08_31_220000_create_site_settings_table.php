<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('site_name', 150);
            $table->string('site_description', 320)->nullable();
            $table->foreignId('logo_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('favicon_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('contact_email', 254)->nullable();
            $table->string('contact_phone', 50)->nullable();
            $table->text('contact_address')->nullable();
            $table->json('social_links')->nullable();
            $table->string('default_seo_title')->nullable();
            $table->string('default_meta_description', 320)->nullable();
            $table->foreignId('default_og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('default_robots_index')->default(true);
            $table->string('timezone', 64);
            $table->string('locale', 16);
            $table->boolean('comments_enabled')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
