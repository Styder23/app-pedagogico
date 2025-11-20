<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitucionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instituciones = [
            [
                'nombre' => 'Institución Educativa San Juan',
                'codigo_ugel' => 'UGEL-001',
                'direccion' => 'Av. Principal 123, San Juan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Colegio Nacional Los Olivos',
                'codigo_ugel' => 'UGEL-002',
                'direccion' => 'Jr. Los Olivos 456, Lima',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Institución Educativa Santa María',
                'codigo_ugel' => 'UGEL-003',
                'direccion' => 'Calle Santa María 789, Arequipa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('instituciones')->insert($instituciones);
    }
}

