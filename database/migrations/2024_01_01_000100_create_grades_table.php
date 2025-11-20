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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->decimal('nota', 5, 2); // Nota de 0 a 20 (o el sistema que usen)
            $table->date('fecha_evaluacion');
            $table->text('observaciones')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Un estudiante solo puede tener una nota por evaluación
            $table->unique(['enrollment_id', 'evaluation_id'], 'unique_grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};

