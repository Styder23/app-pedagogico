<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'nombre' => 'Matemática',
                'codigo' => 'MAT',
                'descripcion' => 'Curso de Matemática',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Comunicación',
                'codigo' => 'COM',
                'descripcion' => 'Curso de Comunicación',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Ciencia y Tecnología',
                'codigo' => 'CYT',
                'descripcion' => 'Curso de Ciencia y Tecnología',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Historia',
                'codigo' => 'HIS',
                'descripcion' => 'Curso de Historia del Perú',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Educación Física',
                'codigo' => 'EFI',
                'descripcion' => 'Curso de Educación Física',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Arte y Cultura',
                'codigo' => 'ART',
                'descripcion' => 'Curso de Arte y Cultura',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Inglés',
                'codigo' => 'ING',
                'descripcion' => 'Curso de Inglés',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Formación Ciudadana y Cívica',
                'codigo' => 'FCC',
                'descripcion' => 'Curso de Formación Ciudadana y Cívica',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('courses')->insert($courses);
    }
}

