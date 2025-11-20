<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Docentes
        $docentes = [
            [
                'tipo' => 'docente',
                'nombres' => 'María',
                'apellidos' => 'García López',
                'dni' => '12345678',
                'fecha_nacimiento' => '1980-05-15',
                'direccion' => 'Av. Principal 100',
                'telefono' => '987654321',
                'email' => 'maria.garcia@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'docente',
                'nombres' => 'Juan',
                'apellidos' => 'Pérez Rodríguez',
                'dni' => '23456789',
                'fecha_nacimiento' => '1975-08-20',
                'direccion' => 'Jr. Los Olivos 200',
                'telefono' => '987654322',
                'email' => 'juan.perez@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'docente',
                'nombres' => 'Carmen',
                'apellidos' => 'Sánchez Torres',
                'dni' => '34567890',
                'fecha_nacimiento' => '1982-03-10',
                'direccion' => 'Calle San Martín 300',
                'telefono' => '987654323',
                'email' => 'carmen.sanchez@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'docente',
                'nombres' => 'Carlos',
                'apellidos' => 'Ramírez Mendoza',
                'dni' => '45678901',
                'fecha_nacimiento' => '1978-11-25',
                'direccion' => 'Av. Libertad 400',
                'telefono' => '987654324',
                'email' => 'carlos.ramirez@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Estudiantes
        $estudiantes = [
            [
                'tipo' => 'estudiante',
                'nombres' => 'Ana',
                'apellidos' => 'González Flores',
                'dni' => '56789012',
                'fecha_nacimiento' => '2010-02-14',
                'direccion' => 'Jr. Primavera 101',
                'telefono' => '987654325',
                'email' => 'ana.gonzalez@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'estudiante',
                'nombres' => 'Luis',
                'apellidos' => 'Martínez Vargas',
                'dni' => '67890123',
                'fecha_nacimiento' => '2010-06-18',
                'direccion' => 'Av. Los Rosales 102',
                'telefono' => '987654326',
                'email' => 'luis.martinez@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'estudiante',
                'nombres' => 'Sofía',
                'apellidos' => 'Herrera Díaz',
                'dni' => '78901234',
                'fecha_nacimiento' => '2010-09-22',
                'direccion' => 'Calle Las Flores 103',
                'telefono' => '987654327',
                'email' => 'sofia.herrera@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'estudiante',
                'nombres' => 'Diego',
                'apellidos' => 'Morales Castro',
                'dni' => '89012345',
                'fecha_nacimiento' => '2010-12-05',
                'direccion' => 'Jr. Los Jardines 104',
                'telefono' => '987654328',
                'email' => 'diego.morales@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'estudiante',
                'nombres' => 'Valentina',
                'apellidos' => 'Ruiz Jiménez',
                'dni' => '90123456',
                'fecha_nacimiento' => '2011-01-30',
                'direccion' => 'Av. Los Pinos 105',
                'telefono' => '987654329',
                'email' => 'valentina.ruiz@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tipo' => 'estudiante',
                'nombres' => 'Mateo',
                'apellidos' => 'Torres Silva',
                'dni' => '01234567',
                'fecha_nacimiento' => '2011-04-12',
                'direccion' => 'Calle Los Alamos 106',
                'telefono' => '987654330',
                'email' => 'mateo.torres@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('personas')->insert($docentes);
        DB::table('personas')->insert($estudiantes);
    }
}

