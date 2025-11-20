<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Server;
use App\Models\Report;
use App\Models\ServerStatus;

class ServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear servidores de ejemplo
        $servers = [
            [
                'name' => 'Servidor Web Principal',
                'url' => 'https://example.com',
                'type' => 'web',
                'description' => 'Servidor web principal de la aplicación',
                'is_active' => true,
                'last_checked' => now()
            ],
            [
                'name' => 'API de Usuarios',
                'url' => 'https://api.example.com/users',
                'type' => 'api',
                'description' => 'API para gestión de usuarios',
                'is_active' => true,
                'last_checked' => now()
            ],
            [
                'name' => 'Base de Datos MySQL',
                'url' => 'mysql://localhost:3306',
                'type' => 'bd',
                'description' => 'Base de datos principal MySQL',
                'is_active' => true,
                'last_checked' => now()
            ]
        ];

        foreach ($servers as $serverData) {
            $server = Server::create($serverData);

            // Crear estado inicial del servidor
            ServerStatus::create([
                'server_id' => $server->id,
                'status' => 'online',
                'response_time' => rand(50, 200),
                'http_status_code' => 200,
                'checked_at' => now()
            ]);

            // Crear algunos reportes de ejemplo
            if (rand(0, 1)) {
                Report::create([
                    'server_id' => $server->id,
                    'servidor_nombre' => $server->name,
                    'fecha' => now()->subDays(rand(1, 30)),
                    'hora' => now()->subHours(rand(1, 24)),
                    'duracion' => rand(5, 120) . ' minutos',
                    'error_descripcion' => 'Error de conexión temporal',
                    'resuelto_por' => 'Sistema Automático',
                    'status' => 'resuelto',
                    'notes' => 'Problema resuelto automáticamente'
                ]);
            }
        }
    }
}
