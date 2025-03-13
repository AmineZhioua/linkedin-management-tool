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
        Schema::table('linkedin_users', function (Blueprint $table) {
            // Ensure a user can have multiple LinkedIn accounts
            $table->index(['user_id', 'linkedin_id'])->after('id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linkedin_users', function (Blueprint $table) {
            //
        });
    }
};
