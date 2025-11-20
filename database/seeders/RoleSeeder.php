<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'description' => 'Administrador del sistema con acceso completo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Director',
                'description' => 'Director de la institución educativa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Docente',
                'description' => 'Docente que imparte clases',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Estudiante',
                'description' => 'Estudiante del sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Secretario',
                'description' => 'Secretario administrativo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'UGEL',
                'description' => 'Usuario de UGEL con acceso a documentos de instituciones',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }
}

