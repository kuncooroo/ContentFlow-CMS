<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk', 64);
            $table->string('path', 1024);
            $table->string('original_name', 255);
            $table->string('file_name', 255);
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text', 500)->nullable();
            $table->timestamps();

            $table->index(['uploaded_by_user_id', 'created_at']);
            $table->index('original_name');
        });

        // Prefix index: full (disk, path) exceeds InnoDB utf8mb4 key limit (3072 bytes).
        DB::statement('ALTER TABLE `media` ADD UNIQUE `media_disk_path_unique` (`disk`, `path`(704))');

        Schema::create('media_references', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->string('owner_type', 80);
            $table->unsignedBigInteger('owner_id');
            $table->string('usage', 80);
            $table->timestamp('created_at');

            $table->unique(['media_id', 'owner_type', 'owner_id', 'usage']);
            $table->index(['owner_type', 'owner_id']);
            $table->index('media_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_references');
        Schema::dropIfExists('media');
    }
};
