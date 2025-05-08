<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boostinteractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('linkedin_user_id')->constrained('linkedin_users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('scheduled_linkedin_posts')->onDelete('cascade');
            $table->string('post_url');
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boostinteractions');
    }
};