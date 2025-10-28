<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditoriaKanbanModel;
use App\Models\UsuarioModel;

/**
 * Controlador de Historial - Auditoría del Kanban
 * Maneja la visualización del registro de actividades del tablero Kanban
 */
class Historial extends BaseController
{
    protected AuditoriaKanbanModel $auditoriaModel;
    protected UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->auditoriaModel = new AuditoriaKanbanModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Vista principal del registro de actividad del Kanban
     */
    public function index(): string
    {
        // Obtener filtros de la URL
        $filtroFecha = $this->request->getGet('fecha') ?? 'hoy';
        $filtroUsuario = $this->request->getGet('usuario') ?? 'todos';

        // Obtener historial de auditoría
        $historial = $this->auditoriaModel->getHistorialCompleto($filtroFecha, $filtroUsuario, 50);

        // Obtener usuarios activos para el filtro
        $usuariosActivos = $this->auditoriaModel->getUsuariosActivos();

        // Obtener estadísticas
        $estadisticas = $this->auditoriaModel->getEstadisticas($filtroFecha);

        $data = [
            'title' => 'Registro de Actividad - ISHUME',
            'historial' => $historial,
            'usuarios_activos' => $usuariosActivos,
            'estadisticas' => $estadisticas,
            'filtro_fecha' => $filtroFecha,
            'filtro_usuario' => $filtroUsuario,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('historial/index', $data);
    }

    /**
     * Obtener historial via AJAX con filtros
     */
    public function obtenerHistorial()
    {
        // Log para debugging
        log_message('info', 'obtenerHistorial llamado - AJAX: ' . ($this->request->isAJAX() ? 'sí' : 'no'));
        log_message('info', 'Usuario en sesión: ' . json_encode([
            'usuario_logueado' => session()->get('usuario_logueado'),
            'usuario_id' => session()->get('usuario_id'),
            'tipo_usuario' => session()->get('tipo_usuario')
        ]));
        
        if (!$this->request->isAJAX()) {
            log_message('error', 'Petición no es AJAX');
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Petición no válida'
            ]);
        }

        try {
            $filtroFecha = $this->request->getPost('fecha') ?? 'hoy';
            $filtroUsuario = $this->request->getPost('usuario') ?? 'todos';
            $limite = (int)($this->request->getPost('limite') ?? 50);
            
            log_message('info', "Filtros: fecha={$filtroFecha}, usuario={$filtroUsuario}, limite={$limite}");
            
            $historial = $this->auditoriaModel->getHistorialCompleto($filtroFecha, $filtroUsuario, $limite);
            log_message('info', 'Registros encontrados: ' . count($historial));
            
            // Formatear historial para la vista
            $historialFormateado = array_map(function($item) {
                return [
                    'id' => $item->id,
                    'fecha' => date('H:i', strtotime($item->fecha)),
                    'fecha_completa' => date('d/m/Y H:i', strtotime($item->fecha)),
                    'usuario' => $item->usuario_nombre,
                    'accion' => $item->accion,
                    'estado_anterior' => $item->estado_anterior,
                    'estado_nuevo' => $item->estado_nuevo,
                    'servicio' => $item->servicio,
                    'categoria' => $item->categoria,
                    'cliente' => $item->cliente_nombre,
                    'descripcion' => $item->equipo_descripcion
                ];
            }, $historial);

            return $this->response->setJSON([
                'success' => true,
                'historial' => $historialFormateado,
                'total' => count($historialFormateado)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo historial: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al obtener historial'
            ]);
        }
    }

    /**
     * Formatear fecha relativa (hace X tiempo)
     */
    private function formatearFechaRelativa(string $fecha): string
    {
        $tiempo = time() - strtotime($fecha);
        
        if ($tiempo < 60) {
            return 'hace unos segundos';
        } elseif ($tiempo < 3600) {
            $minutos = floor($tiempo / 60);
            return "hace {$minutos} minuto" . ($minutos > 1 ? 's' : '');
        } elseif ($tiempo < 86400) {
            $horas = floor($tiempo / 3600);
            return "hace {$horas} hora" . ($horas > 1 ? 's' : '');
        } else {
            $dias = floor($tiempo / 86400);
            return "hace {$dias} día" . ($dias > 1 ? 's' : '');
        }
    }

    /**
     * Obtener icono según el tipo de servicio
     */
    private function obtenerIconoServicio(string $categoria): string
    {
        return match(strtolower($categoria)) {
            'audio y sonido' => 'volume-2',
            'fotografía y video' => 'camera',
            'iluminación' => 'lightbulb',
            'decoración' => 'palette',
            'catering' => 'utensils',
            default => 'settings'
        };
    }

    /**
     * Obtener color según el estado
     */
    private function obtenerColorEstado(string $estado): string
    {
        return match(strtolower($estado)) {
            'pendiente', 'programado' => 'warning',
            'en proceso' => 'primary',
            'completado' => 'success',
            default => 'secondary'
        };
    }
}
