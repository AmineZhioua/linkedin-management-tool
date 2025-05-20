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
        Schema::table('subscriptions', function (Blueprint $table) {
            // Drop the features column
            $table->dropColumn('features');
            // Add boost_likes and available_posts columns
            $table->integer('boost_likes')->default(0);
            $table->integer('available_posts')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Restore the features column
            $table->json('features');
            // Drop the new columns
            $table->dropColumn(['boost_likes', 'available_posts']);
        });
    }
};