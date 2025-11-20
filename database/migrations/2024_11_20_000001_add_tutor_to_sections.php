<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('tutor_persona_id')->nullable()->after('anio_escolar')
                ->constrained('personas')->onDelete('set null')
                ->comment('Docente tutor a cargo de la sección');
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['tutor_persona_id']);
            $table->dropColumn('tutor_persona_id');
        });
    }
};

