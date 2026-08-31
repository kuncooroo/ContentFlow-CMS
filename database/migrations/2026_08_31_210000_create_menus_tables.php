<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menus', function (Blueprint $table): void {
            $table->id();
            $table->string('key', 80)->unique();
            $table->string('name', 120);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('label', 150);
            $table->foreignId('page_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('custom_url', 2048)->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['menu_id', 'position']);
            $table->index('page_id');
            $table->index('post_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
    }
};
