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
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->string('grado', 10); // Ej: "1", "2", "3", etc.
            $table->string('seccion', 5); // Ej: "A", "B", "C", etc.
            // El nombre completo (1A, 2B, etc.) se puede generar con un accessor en el modelo
            $table->integer('anio_escolar')->nullable();
            $table->timestamps();
            
            // Asegurar que no haya secciones duplicadas en la misma institución
            $table->unique(['institucion_id', 'grado', 'seccion', 'anio_escolar'], 'unique_section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};

