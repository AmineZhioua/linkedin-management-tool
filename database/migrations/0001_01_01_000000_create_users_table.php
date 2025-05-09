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
        // Create the users table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index()->comment('User full name');
            $table->string('email')->unique()->comment('User email address');
            $table->timestamp('email_verified_at')->nullable()->comment('Email verification timestamp');
            $table->string('password')->nullable()->comment('Hashed password');
            $table->string('google_id')->nullable()->comment('Google OAuth ID');
            $table->string('role')->default('user')->index()->comment('User role (e.g., user, admin)');
            $table->boolean('post_perm')->default(false)->comment('Permission to create posts');
            $table->boolean('boost_perm')->default(false)->comment('Permission to boost interactions');
            $table->string('image')->nullable()->comment('Profile image URL');
            $table->timestamp('last_activity')->nullable()->index()->comment('Last user activity timestamp');
            $table->rememberToken()->comment('Token for remember me functionality');
            $table->timestamp('created_at')->nullable()->comment('Record creation timestamp');
            $table->timestamp('updated_at')->nullable()->comment('Record update timestamp');
        });

        // Create the password_reset_tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary()->comment('Email address for password reset');
            $table->string('token')->comment('Password reset token');
            $table->timestamp('created_at')->nullable()->comment('Token creation timestamp');
        });

        // Create the sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary()->comment('Session ID');
            $table->foreignId('user_id')->nullable()->index()->comment('Associated user ID');
            $table->string('ip_address', 45)->nullable()->comment('Client IP address');
            $table->text('user_agent')->nullable()->comment('Client user agent');
            $table->longText('payload')->comment('Session data payload');
            $table->integer('last_activity')->index()->comment('Last session activity timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};