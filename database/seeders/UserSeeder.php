<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener IDs de roles
        $adminRole = DB::table('roles')->where('name', 'Administrador')->first()->id;
        $docenteRole = DB::table('roles')->where('name', 'Docente')->first()->id;
        $estudianteRole = DB::table('roles')->where('name', 'Estudiante')->first()->id;

        // Obtener personas
        $personas = DB::table('personas')->get();

        $users = [];

        // Crear usuario administrador
        $adminPersona = DB::table('personas')->where('tipo', 'docente')->first();
        $users[] = [
            'name' => $adminPersona->nombres . ' ' . $adminPersona->apellidos,
            'role_id' => $adminRole,
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'persona_id' => $adminPersona->id,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Crear usuarios para docentes
        $docentes = $personas->where('tipo', 'docente');
        foreach ($docentes as $docente) {
            $email = $docente->email ?: "docente{$docente->id}@example.com";
            $users[] = [
                'name' => $docente->nombres . ' ' . $docente->apellidos,
                'role_id' => $docenteRole,
                'email' => $email,
                'password' => Hash::make('password'),
                'persona_id' => $docente->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Crear usuarios para estudiantes (algunos)
        $estudiantes = $personas->where('tipo', 'estudiante')->take(3);
        foreach ($estudiantes as $estudiante) {
            $email = $estudiante->email ?: "estudiante{$estudiante->id}@example.com";
            $users[] = [
                'name' => $estudiante->nombres . ' ' . $estudiante->apellidos,
                'role_id' => $estudianteRole,
                'email' => $email,
                'password' => Hash::make('password'),
                'persona_id' => $estudiante->id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);
    }
}

