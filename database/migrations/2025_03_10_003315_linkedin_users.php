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
        Schema::create('linkedin_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('linkedin_id')->nullable();
            $table->string('linkedin_firstname')->nullable();
            $table->string('linkedin_lastname')->nullable();
            $table->text('linkedin_picture')->nullable();
            $table->text('linkedin_token')->nullable();
            $table->text('linkedin_refresh_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linkedin_users');
    }
};
