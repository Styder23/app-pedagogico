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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->string('tipo_prediccion', 100); // Ej: "rendimiento_final", "riesgo_abandono", "probabilidad_aprobacion"
            $table->decimal('valor_predicho', 10, 4)->nullable(); // Valor numérico predicho
            $table->string('categoria_predicha', 100)->nullable(); // Categoría predicha (ej: "alto", "medio", "bajo")
            $table->decimal('confianza', 5, 2)->nullable(); // Nivel de confianza (0-100)
            $table->json('factores_considerados')->nullable(); // Factores que se consideraron
            $table->text('observaciones')->nullable();
            $table->date('fecha_prediccion');
            $table->date('fecha_validez_hasta')->nullable(); // Hasta cuándo es válida esta predicción
            $table->boolean('validada')->default(false); // Si la predicción fue validada con datos reales
            $table->timestamps();
            
            // Índices
            $table->index('enrollment_id');
            $table->index('tipo_prediccion');
            $table->index('fecha_prediccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};

