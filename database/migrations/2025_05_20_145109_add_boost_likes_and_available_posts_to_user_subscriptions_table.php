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
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->integer('boost_likes')->default(0)->after('pricing_mode');
            $table->integer('available_posts')->default(0)->after('boost_likes');
            $table->integer('boost_comments')->default(0)->after('available_posts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['boost_likes', 'available_posts']);
        });
    }
};