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
            $table->foreignId('campaign_id')->nullable()->constrained('linkedin_campaigns')->onDelete('cascade');
            $table->foreignId('job_id')->nullable()->constrained('jobs')->onDelete('set null');
            $table->string('type');
            $table->json('content');
            $table->dateTime('scheduled_time');
            $table->enum('status', ['queued', 'posted', 'failed', 'draft'])->default('queued');
            $table->text('error')->nullable();
            $table->string('post_urn')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('scheduled_linkedin_posts');
        Schema::enableForeignKeyConstraints();
    }
};
