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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tardanza', 'justificado'])->default('presente');
            $table->text('justificacion')->nullable();
            $table->foreignId('registrado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Un estudiante solo puede tener un registro de asistencia por día
            $table->unique(['enrollment_id', 'fecha'], 'unique_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};

