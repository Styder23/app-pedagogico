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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade'); // estudiante
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->date('fecha_inscripcion');
            $table->enum('estado', ['activo', 'inactivo', 'egresado', 'retirado'])->default('activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Un estudiante solo puede estar inscrito una vez en la misma sección
            $table->unique(['persona_id', 'section_id'], 'unique_enrollment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};

