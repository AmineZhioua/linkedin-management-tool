<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('linkedin_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('linkedin_user_id')->constrained('linkedin_users')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('target_audience');
            $table->integer('frequency_per_day');
            $table->string('color');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->enum('status', ['draft', 'scheduled', 'completed', 'failed'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('linkedin_campaigns');
        Schema::enableForeignKeyConstraints();
    }
};