<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->timestamp('start_at')->nullable()->after('message');
            $table->timestamp('end_at')->nullable()->after('start_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('alerts', function (Blueprint $table) {
            $table->dropColumn(['start_at', 'end_at']);
        });
    }
};