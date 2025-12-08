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
            'rol' => 'administrador',
            'bio' => 'Administrador del sistema HackZone. Encargado de la gestión y organización de eventos de programación.',
            'telefono' => '+52 951 123 4567',
            'ubicacion' => 'Oaxaca, México',
            'habilidades' => ['Gestión de proyectos', 'Administración', 'Organización de eventos']
        ]);

        // Juez 1
        User::create([
            'name' => 'Dr. Carlos Rodríguez',
            'username' => 'carlos.juez',
            'email' => 'carlos@hackzone.com',
            'password' => Hash::make('password123'),
            'rol' => 'juez',
            'bio' => 'Doctor en Ciencias de la Computación. Especialista en Inteligencia Artificial y Machine Learning.',
            'telefono' => '+52 951 234 5678',
            'ubicacion' => 'Oaxaca, México',
            'habilidades' => ['Inteligencia Artificial', 'Python', 'Machine Learning', 'Investigación']
        ]);

        // Juez 2
        User::create([
            'name' => 'Ing. María González',
            'username' => 'maria.juez',
            'email' => 'maria@hackzone.com',
            'password' => Hash::make('password123'),
            'rol' => 'juez',
            'bio' => 'Ingeniera en Sistemas con 10 años de experiencia en desarrollo web y evaluación de proyectos.',
            'telefono' => '+52 951 345 6789',
            'ubicacion' => 'Oaxaca, México',
            'habilidades' => ['Desarrollo Web', 'JavaScript', 'React', 'Node.js', 'Evaluación técnica']
        ]);

        // Usuarios normales
        $usuarios = [
            [
                'name' => 'Julian Torres',
                'username' => 'julian',
                'email' => 'julian@gmail.com',
                'bio' => 'Amante de la programación, me encanta el lenguaje Java y un poco el C++, me gusta participar en hackatones y crear soluciones innovadoras.',
                'telefono' => '+52 951 789 6539',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['JavaScript', 'Python', 'C++', 'PostgreSQL', 'Trabajo en equipo']
            ],
            [
                'name' => 'Ana Martínez',
                'username' => 'ana.m',
                'email' => 'ana@gmail.com',
                'bio' => 'Desarrolladora full-stack apasionada por crear experiencias web increíbles.',
                'telefono' => '+52 951 456 7890',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['React', 'Node.js', 'MongoDB', 'CSS', 'UI/UX']
            ],
            [
                'name' => 'Pedro Sánchez',
                'username' => 'pedro.s',
                'email' => 'pedro@gmail.com',
                'bio' => 'Ingeniero de software especializado en backend y arquitectura de sistemas.',
                'telefono' => '+52 951 567 8901',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['Java', 'Spring Boot', 'Docker', 'Microservicios', 'AWS']
            ],
            [
                'name' => 'Laura García',
                'username' => 'laura.g', 'email' => 'laura@gmail.com',
                'bio' => 'Data scientist enfocada en análisis de datos y machine learning.',
                'telefono' => '+52 951 678 9012',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['Python', 'TensorFlow', 'Pandas', 'SQL', 'Visualización de datos']
            ],
            [
                'name' => 'Miguel Ramírez',
                'username' => 'miguel.r',
                'email' => 'miguel@gmail.com',
                'bio' => 'Desarrollador móvil con experiencia en Flutter y React Native.',
                'telefono' => '+52 951 789 0123',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['Flutter', 'React Native', 'Firebase', 'Kotlin', 'Swift']
            ],
            [
                'name' => 'Sofia López',
                'username' => 'sofia.l',
                'email' => 'sofia@gmail.com',
                'bio' => 'Diseñadora UX/UI con pasión por crear interfaces intuitivas y atractivas.',
                'telefono' => '+52 951 890 1234',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['Figma', 'Adobe XD', 'Prototipado', 'User Research', 'HTML/CSS']
            ],
            [
                'name' => 'David Hernández',
                'username' => 'david.h',
                'email' => 'david@gmail.com',
                'bio' => 'DevOps engineer enfocado en automatización y despliegue continuo.',
                'telefono' => '+52 951 901 2345',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['Jenkins', 'Kubernetes', 'Terraform', 'CI/CD', 'Linux']
            ],
            [
                'name' => 'Carla Díaz',
                'username' => 'carla.d',
                'email' => 'carla@gmail.com',
                'bio' => 'Desarrolladora frontend especializada en Vue.js y animaciones web.',
                'telefono' => '+52 951 012 3456',
                'ubicacion' => 'Oaxaca, México',
                'habilidades' => ['Vue.js', 'TypeScript', 'GSAP', 'Sass', 'Responsive Design']
            ],
        ];

        foreach ($usuarios as $userData) {
            User::create([
                'name' => $userData['name'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'rol' => 'usuario',
                'bio' => $userData['bio'],
                'telefono' => $userData['telefono'],
                'ubicacion' => $userData['ubicacion'],
                'habilidades' => $userData['habilidades']
            ]);
        }
    }
}