<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HistorialActividadesSeeder extends Seeder
{
    public function run()
    {
        // Obtener algunos IDs existentes para los ejemplos
        $usuarios = $this->db->table('usuarios')->select('idusuario')->limit(3)->get()->getResultArray();
        $servicios = $this->db->table('servicioscontratados')->select('idserviciocontratado')->limit(5)->get()->getResultArray();
        
        if (empty($usuarios) || empty($servicios)) {
            echo "No hay usuarios o servicios disponibles para crear datos de ejemplo.\n";
            return;
        }

        // Datos de ejemplo para el historial
        $actividades = [
            [
                'tabla_afectada' => 'servicioscontratados',
                'registro_id' => $servicios[0]['idserviciocontratado'],
                'accion' => 'cambio_estado',
                'campo_modificado' => 'estado',
                'valor_anterior' => '"Pendiente"',
                'valor_nuevo' => '"En Proceso"',
                'descripcion' => 'El estado cambió de \'Pendiente\' a \'En Proceso\' en \'Evento Corporativo\' por Ricardo A.',
                'usuario_id' => $usuarios[0]['idusuario'],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode([
                    'contexto' => 'Evento Corporativo',
                    'timestamp' => date('Y-m-d H:i:s', strtotime('-2 days'))
                ]),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'tabla_afectada' => 'servicioscontratados',
                'registro_id' => $servicios[0]['idserviciocontratado'],
                'accion' => 'subir_archivo',
                'descripcion' => 'El técnico Ricardo A. subió archivos de audio para \'Evento Corporativo\'',
                'usuario_id' => $usuarios[0]['idusuario'],
                'ip_address' => '192.168.1.100',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode([
                    'tipo_archivo' => 'audio',
                    'nombre_archivo' => 'audio_evento_corporativo.mp3',
                    'contexto' => 'Evento Corporativo',
                    'fecha_subida' => date('Y-m-d H:i:s', strtotime('-1 day 6 hours'))
                ]),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day 6 hours'))
            ],
            [
                'tabla_afectada' => 'servicioscontratados',
                'registro_id' => $servicios[0]['idserviciocontratado'],
                'accion' => 'completar',
                'campo_modificado' => 'estado',
                'valor_nuevo' => '"Completado"',
                'descripcion' => 'El proyecto \'Evento Corporativo\' fue marcado como completado por Carlos G.',
                'usuario_id' => $usuarios[1]['idusuario'],
                'ip_address' => '192.168.1.101',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode([
                    'proyecto' => 'Evento Corporativo',
                    'fecha_completacion' => date('Y-m-d H:i:s', strtotime('-1 day'))
                ]),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ]
        ];

        // Agregar más actividades si hay más servicios
        if (count($servicios) > 1) {
            $actividades[] = [
                'tabla_afectada' => 'equipos',
                'registro_id' => 1, // Asumiendo que existe equipo con ID 1
                'accion' => 'asignar',
                'descripcion' => 'Equipo asignado a Carmen R. para \'Boda Jardín\'',
                'usuario_id' => $usuarios[2]['idusuario'] ?? $usuarios[0]['idusuario'],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode([
                    'servicio_id' => $servicios[1]['idserviciocontratado'],
                    'tecnico' => 'Carmen R.',
                    'contexto' => 'Boda Jardín'
                ]),
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 hours'))
            ];

            $actividades[] = [
                'tabla_afectada' => 'servicioscontratados',
                'registro_id' => $servicios[1]['idserviciocontratado'],
                'accion' => 'comentario',
                'descripcion' => 'Carmen R. agregó un comentario en \'Boda Jardín\'',
                'usuario_id' => $usuarios[2]['idusuario'] ?? $usuarios[0]['idusuario'],
                'ip_address' => '192.168.1.102',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'metadata' => json_encode([
                    'comentario' => 'Equipo de sonido configurado correctamente. Listo para el evento.',
                    'contexto' => 'Boda Jardín'
                ]),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ];
        }

        // Insertar los datos
        foreach ($actividades as $actividad) {
            $this->db->table('historial_actividades')->insert($actividad);
        }

        echo "Se insertaron " . count($actividades) . " actividades de ejemplo en el historial.\n";
    }
}
