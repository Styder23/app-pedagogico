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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('tabla', 100); // Nombre de la tabla afectada
            $table->unsignedBigInteger('registro_id'); // ID del registro afectado
            $table->enum('accion', ['create', 'update', 'delete', 'restore']);
            $table->foreignId('usuario_id')->nullable()->constrained('users')->onDelete('set null');
            $table->json('datos_anteriores')->nullable(); // Datos antes del cambio
            $table->json('datos_nuevos')->nullable(); // Datos después del cambio
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas rápidas
            $table->index(['tabla', 'registro_id']);
            $table->index('usuario_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};

