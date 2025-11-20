<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enrollments
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropForeign(['section_id']);
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->unsignedBigInteger('persona_id')->nullable()->change();
            $table->unsignedBigInteger('section_id')->nullable()->change();
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('set null');

            $table->foreignId('institucion_id')->nullable()->after('section_id')->constrained('instituciones')->nullOnDelete();
            $table->string('grado', 20)->nullable()->after('institucion_id');
            $table->string('seccion', 10)->nullable()->after('grado');
            $table->string('archivo_path')->nullable()->after('observaciones');
            $table->string('archivo_formato', 10)->nullable()->after('archivo_path');
            $table->boolean('es_carga')->default(false)->after('archivo_formato');
            $table->foreignId('usuario_id')->nullable()->after('es_carga')->constrained('users')->nullOnDelete();
        });

        // Attendance
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->unsignedBigInteger('enrollment_id')->nullable()->change();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('set null');

            $table->foreignId('institucion_id')->nullable()->after('enrollment_id')->constrained('instituciones')->nullOnDelete();
            $table->string('grado', 20)->nullable()->after('institucion_id');
            $table->string('seccion', 10)->nullable()->after('grado');
            $table->string('archivo_path')->nullable()->after('justificacion');
            $table->string('archivo_formato', 10)->nullable()->after('archivo_path');
            $table->boolean('es_carga')->default(false)->after('archivo_formato');
        });

        // Evaluations
        Schema::table('evaluations', function (Blueprint $table) {
            $table->foreignId('institucion_id')->nullable()->after('orden')->constrained('instituciones')->nullOnDelete();
            $table->string('grado', 20)->nullable()->after('institucion_id');
            $table->string('seccion', 10)->nullable()->after('grado');
            $table->string('archivo_path')->nullable()->after('activo');
            $table->string('archivo_formato', 10)->nullable()->after('archivo_path');
            $table->boolean('es_carga')->default(false)->after('archivo_formato');
            $table->foreignId('usuario_id')->nullable()->after('es_carga')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropForeign(['institucion_id']);
            $table->dropColumn(['usuario_id', 'es_carga', 'archivo_formato', 'archivo_path', 'seccion', 'grado', 'institucion_id']);

            $table->dropForeign(['persona_id']);
            $table->dropForeign(['section_id']);
            $table->unsignedBigInteger('persona_id')->nullable(false)->change();
            $table->unsignedBigInteger('section_id')->nullable(false)->change();
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });

        Schema::table('attendance', function (Blueprint $table) {
            $table->dropForeign(['institucion_id']);
            $table->dropColumn(['institucion_id', 'grado', 'seccion', 'archivo_path', 'archivo_formato', 'es_carga']);

            $table->dropForeign(['enrollment_id']);
            $table->unsignedBigInteger('enrollment_id')->nullable(false)->change();
            $table->foreign('enrollment_id')->references('id')->on('enrollments')->onDelete('cascade');
        });

        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['institucion_id']);
            $table->dropForeign(['usuario_id']);
            $table->dropColumn(['institucion_id', 'grado', 'seccion', 'archivo_path', 'archivo_formato', 'es_carga', 'usuario_id']);
        });
    }
};
