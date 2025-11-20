<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener la primera institución
        $institucionId = DB::table('instituciones')->first()->id;
        $anioEscolar = date('Y');

        $sections = [
            // Primer grado
            [
                'institucion_id' => $institucionId,
                'grado' => '1',
                'seccion' => 'A',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institucion_id' => $institucionId,
                'grado' => '1',
                'seccion' => 'B',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Segundo grado
            [
                'institucion_id' => $institucionId,
                'grado' => '2',
                'seccion' => 'A',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institucion_id' => $institucionId,
                'grado' => '2',
                'seccion' => 'B',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tercer grado
            [
                'institucion_id' => $institucionId,
                'grado' => '3',
                'seccion' => 'A',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'institucion_id' => $institucionId,
                'grado' => '3',
                'seccion' => 'B',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Cuarto grado
            [
                'institucion_id' => $institucionId,
                'grado' => '4',
                'seccion' => 'A',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Quinto grado
            [
                'institucion_id' => $institucionId,
                'grado' => '5',
                'seccion' => 'A',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Sexto grado
            [
                'institucion_id' => $institucionId,
                'grado' => '6',
                'seccion' => 'A',
                'anio_escolar' => $anioEscolar,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('sections')->insert($sections);
    }
}

