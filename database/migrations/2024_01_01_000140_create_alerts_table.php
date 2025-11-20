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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 50); // Ej: "bajo_rendimiento", "inasistencia", "riesgo_abandono"
            $table->enum('nivel', ['info', 'warning', 'critical'])->default('info');
            $table->string('titulo', 255);
            $table->text('mensaje');
            $table->string('tabla_relacionada', 100)->nullable(); // Tabla relacionada (ej: "enrollments")
            $table->unsignedBigInteger('registro_relacionado_id')->nullable(); // ID del registro relacionado
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null'); // Usuario al que va dirigida
            $table->boolean('leida')->default(false);
            $table->timestamp('fecha_leida')->nullable();
            $table->json('datos_adicionales')->nullable(); // Datos adicionales en JSON
            $table->timestamps();
            
            // Índices
            $table->index(['usuario_id', 'leida']);
            $table->index(['tabla_relacionada', 'registro_relacionado_id']);
            $table->index('nivel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alerts');
    }
};

