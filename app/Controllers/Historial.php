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
                'accion' => $this->obtenerTextoAccion($item),
                'detalles' => [
                    'equipo' => $item->equipo_descripcion,
                    'servicio' => $item->servicio,
                    'categoria' => $item->categoria,
                    'cliente' => $item->cliente_nombre,
                    'estado_anterior' => $item->estado_anterior,
                    'estado_nuevo' => $item->estado_nuevo
                ]
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

    // Obtener texto descriptivo de la acción realizada
    private function obtenerTextoAccion(object $item): string
    {
        switch ($item->accion) {
            case 'cambiar_estado':
                return "Cambió estado de '{$item->estado_anterior}' a '{$item->estado_nuevo}'";
            
            case 'crear':
                return "Creó nuevo equipo";
            
            case 'reasignar':
                return "Reasignó equipo";
            
            default:
                return ucfirst($item->accion);
        }
    }
}