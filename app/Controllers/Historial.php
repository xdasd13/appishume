<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AuditoriaKanbanModel;
use App\Models\UsuarioModel;

// Controlador de Historial de Actividades
class Historial extends BaseController
{
    private AuditoriaKanbanModel $auditoriaModel;
    private UsuarioModel $usuarioModel;

    // Constructor
    public function __construct()
    {
        $this->auditoriaModel = new AuditoriaKanbanModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Página principal del historial de actividades
     * 
     * Muestra una tabla con todos los cambios realizados en el tablero Kanban.
     * Incluye filtro de búsqueda por usuario.
     * 
     * @return string Vista HTML del historial
     */
    public function index(): string
    {        
        // Obtener filtro de usuario desde la URL (si existe)
        $filtroUsuario = $this->request->getGet('usuario') ?? 'todos';

        // Obtener todo el historial de actividades
        $historial = $this->auditoriaModel->obtenerTodoElHistorial($filtroUsuario);

        // Obtener lista de usuarios para el buscador
        $usuarios = $this->auditoriaModel->obtenerUsuariosActivos();

        // Preparar datos para la vista
        $data = [
            'title' => 'Historial de Actividades - ISHUME',
            'historial' => $historial,
            'usuarios' => $usuarios,
            'filtro_usuario' => $filtroUsuario,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('historial/index', $data);
    }

    /**
     * Obtener historial via AJAX (para búsqueda en tiempo real)
     * 
     * Endpoint para actualizar la tabla de historial sin recargar la página.
     * Recibe el filtro de usuario y retorna los datos en formato JSON.
     * 
     * @return \CodeIgniter\HTTP\ResponseInterface JSON con el historial
     */
    public function buscarHistorial()
    {
        // Verificar que sea una petición AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'mensaje' => 'Petición no válida'
            ]);
        }

        try {
            // Obtener filtro de usuario del POST
            $filtroUsuario = $this->request->getPost('usuario') ?? 'todos';
            
            // Obtener historial filtrado
            $historial = $this->auditoriaModel->obtenerTodoElHistorial($filtroUsuario);
            
            // Formatear datos para la respuesta
            $historialFormateado = $this->formatearHistorial($historial);

            return $this->response->setJSON([
                'success' => true,
                'historial' => $historialFormateado,
                'total' => count($historialFormateado)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en buscarHistorial: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'mensaje' => 'Error al buscar historial'
            ]);
        }
    }

   // Formatear historial para respuesta JSON
    private function formatearHistorial(array $historial): array
    {
        return array_map(function($item) {
            return [
                'id' => $item->id,
                'fecha' => date('d/m/Y', strtotime($item->fecha)),
                'hora' => date('H:i:s', strtotime($item->fecha)),
                'dia' => $this->obtenerNombreDia($item->fecha),
                'usuario' => $item->usuario_nombre,
                'accion_tipo' => $item->accion,
                'accion' => $this->generarTextoAccionHTML($item)
            ];
        }, $historial);
    }

    // Obtener nombre del día de la semana en español
    private function obtenerNombreDia(string $fecha): string
    {
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $numeroDia = date('w', strtotime($fecha));
        return $dias[$numeroDia];
    }

    // Generar HTML completo de la acción con estilos
    private function generarTextoAccionHTML(object $item): string
    {
        $html = '';
        
        switch ($item->accion) {
            case 'cambiar_estado':
                $badgeAnterior = $this->obtenerBadgeEstado($item->estado_anterior);
                $badgeNuevo = $this->obtenerBadgeEstado($item->estado_nuevo);
                
                $html .= '<p class="mb-2"><strong>Cambió estado:</strong> ' . esc($item->equipo_descripcion) . '</p>';
                $html .= '<div class="mb-2">';
                $html .= $badgeAnterior . ' <i class="fas fa-arrow-right mx-2"></i> ' . $badgeNuevo;
                $html .= '</div>';
                $html .= '<div>';
                $html .= '<span class="detail-item"><i class="fas fa-briefcase"></i>' . esc($item->servicio) . '</span>';
                $html .= '<span class="detail-item"><i class="fas fa-user"></i>' . esc($item->cliente_nombre) . '</span>';
                $html .= '</div>';
                break;
            
            case 'crear':
                $html .= '<p class="mb-2"><strong>Creó nuevo equipo:</strong> ' . esc($item->equipo_descripcion) . '</p>';
                $html .= '<span class="detail-item"><i class="fas fa-briefcase"></i>' . esc($item->servicio) . '</span>';
                break;
            
            case 'reasignar':
                $html .= '<p class="mb-2"><strong>Reasignó equipo:</strong> ' . esc($item->equipo_descripcion) . '</p>';
                $html .= '<span class="detail-item"><i class="fas fa-briefcase"></i>' . esc($item->servicio) . '</span>';
                break;
            
            default:
                $html .= '<p>' . ucfirst($item->accion) . '</p>';
        }
        
        return $html;
    }
    
    // Obtener badge HTML según el estado
    private function obtenerBadgeEstado(string $estado): string
    {
        $clases = [
            'Pendiente' => 'badge-pendiente',
            'En Proceso' => 'badge-proceso',
            'Completado' => 'badge-completado',
            'Programado' => 'badge-programado'
        ];
        
        $clase = $clases[$estado] ?? 'badge-pendiente';
        return '<span class="estado-badge ' . $clase . '">' . esc($estado) . '</span>';
    }
}