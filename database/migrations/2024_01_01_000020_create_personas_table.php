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
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['estudiante', 'docente']);
            $table->string('nombres', 150);
            $table->string('apellidos', 150);
            $table->string('dni', 12)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personas');
    }
};

