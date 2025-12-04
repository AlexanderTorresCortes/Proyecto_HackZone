<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador
        User::create([
            'name' => 'Admin HackZone',
            'username' => 'admin',
            'email' => 'admin@hackzone.com',
            'password' => Hash::make('password123'),
            'rol' => 'administrador'
        ]);

        // Juez 1
        User::create([
            'name' => 'Dr. Carlos Rodríguez',
            'username' => 'carlos.juez',
            'email' => 'carlos@hackzone.com',
            'password' => Hash::make('password123'),
            'rol' => 'juez'
        ]);

        // Juez 2
        User::create([
            'name' => 'Ing. María González',
            'username' => 'maria.juez',
            'email' => 'maria@hackzone.com',
            'password' => Hash::make('password123'),
            'rol' => 'juez'
        ]);

        // Usuarios normales
        $usuarios = [
            ['name' => 'Julian Torres', 'username' => 'julian', 'email' => 'julian@gmail.com'],
            ['name' => 'Ana Martínez', 'username' => 'ana.m', 'email' => 'ana@gmail.com'],
            ['name' => 'Pedro Sánchez', 'username' => 'pedro.s', 'email' => 'pedro@gmail.com'],
            ['name' => 'Laura García', 'username' => 'laura.g', 'email' => 'laura@gmail.com'],
            ['name' => 'Miguel Ramírez', 'username' => 'miguel.r', 'email' => 'miguel@gmail.com'],
            ['name' => 'Sofia López', 'username' => 'sofia.l', 'email' => 'sofia@gmail.com'],
            ['name' => 'David Hernández', 'username' => 'david.h', 'email' => 'david@gmail.com'],
            ['name' => 'Carla Díaz', 'username' => 'carla.d', 'email' => 'carla@gmail.com'],
        ];

        foreach ($usuarios as $userData) {
            User::create([
                'name' => $userData['name'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'rol' => 'usuario'
            ]);
        }
    }
}