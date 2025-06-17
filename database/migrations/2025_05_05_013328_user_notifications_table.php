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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('linkedin_user_id')->constrained('linkedin_users')->onDelete('cascade'); // CAN BE CHANGED
            $table->integer('campaign_id')->nullable()->constrained('linkedin_campaigns')->onDelete('cascade');
            $table->string('event_name');
            $table->text('message');
            $table->dateTime('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_notifications');
        Schema::enableForeignKeyConstraints();
    }
};
