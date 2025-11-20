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
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['excel', 'ugel', 'otro']);
            $table->string('archivo', 255)->nullable();
            $table->string('tabla_destino', 100)->nullable(); // Tabla donde se importaron los datos
            $table->integer('registros_totales')->default(0);
            $table->integer('registros_importados')->default(0);
            $table->integer('registros_fallidos')->default(0);
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'fallido'])->default('pendiente');
            $table->text('errores')->nullable(); // JSON con errores detallados
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};

