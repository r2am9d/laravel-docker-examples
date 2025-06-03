<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->string('thumbnail')->nullable();
            $table->string('title');
            $table->string('color');
            $table->string('slug')->unique();
            $table->text('content')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_published')->default(false);
            $table->foreignUlid('category_id')->constrained('categories')->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
