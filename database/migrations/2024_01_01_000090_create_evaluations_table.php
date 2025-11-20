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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->enum('tipo', ['parcial', 'tarea', 'examen', 'practica', 'proyecto', 'otro']);
            $table->text('descripcion')->nullable();
            $table->decimal('peso', 5, 2)->default(1.00); // Peso de la evaluación (ej: 0.30 para 30%)
            $table->integer('orden')->default(0); // Orden de visualización
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};

