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
        Schema::create('data_uploads', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['inscripciones', 'asistencias', 'notas']);
            $table->foreignId('institucion_id')->nullable()->constrained('instituciones')->nullOnDelete();
            $table->string('grado', 20);
            $table->string('seccion', 10);
            $table->enum('evaluacion_tipo', ['examen', 'trabajo', 'tarea'])->nullable();
            $table->string('archivo');
            $table->string('formato', 10);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_uploads');
    }
};
