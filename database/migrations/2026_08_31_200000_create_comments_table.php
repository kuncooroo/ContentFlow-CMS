<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained()->restrictOnDelete();
            $table->foreignId('moderated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name', 150);
            $table->string('author_email', 254);
            $table->longText('content');
            $table->string('status', 20)->default('pending');
            $table->dateTime('moderated_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['post_id', 'status', 'created_at']);
            $table->index('moderated_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
