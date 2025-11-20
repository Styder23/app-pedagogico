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
        Schema::table('users', function (Blueprint $table) {
            // Make name nullable since we now use persona_id
            if (Schema::hasColumn('users', 'name')) {
                $table->string('name')->nullable()->change();
            }
            
            // Add new fields
            $table->foreignId('role_id')->after('id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('persona_id')->nullable()->after('password')->constrained('personas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['persona_id']);
            $table->dropColumn(['role_id', 'persona_id']);
        });
    }
};

