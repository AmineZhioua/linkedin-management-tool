<?php

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
        Schema::create('scheduled_linkedin_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('linkedin_user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // text, image, video, article
            $table->json('content');
            $table->dateTime('scheduled_time');
            $table->enum('status', ['queued', 'posted', 'failed'])->default('queued');
            $table->text('error')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_linkedin_posts');
    }
};
