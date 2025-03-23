<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('plateforme_marque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User ID
            $table->string('nom_marque')->nullable(); // Brand Name
            $table->string('domaine_marque')->nullable(); // Brand Domain
            $table->string('logo_marque')->nullable(); // Brand Logo (File Path)
            $table->text('description_marque')->nullable(); // Brand Description
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('plateforme_marque');
    }
};

