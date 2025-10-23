<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\HistorialHelper;
use App\Models\HistorialActividadesModel;

/**
 * Controlador de Historial - Solo para Administradores
 * Maneja la visualización completa del historial del sistema
 */
class Historial extends BaseController
{
    protected HistorialActividadesModel $historialModel;
    protected HistorialHelper $historialHelper;

    public function __construct()
    {
        $this->historialModel = new HistorialActividadesModel();
        $this->historialHelper = new HistorialHelper();
    }

    /**
     * Vista principal del historial (solo administradores)
     */
    public function index(): string
    {
        // Verificar que el usuario sea administrador
        if (!$this->esAdministrador()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }

        // Obtener filtros de la URL
        $filtros = [
            'tabla' => $this->request->getGet('tabla'),
            'accion' => $this->request->getGet('accion'),
            'usuario_id' => $this->request->getGet('usuario_id'),
            'fecha_desde' => $this->request->getGet('fecha_desde') ?? date('Y-m-01'),
            'fecha_hasta' => $this->request->getGet('fecha_hasta') ?? date('Y-m-d')
        ];

        // Obtener actividades recientes con filtros (usando query builder directo)
        $db = \Config\Database::connect();
        $builder = $db->table('historial_actividades ha')
                      ->select('ha.*, u.nombreusuario, p.nombres, p.apellidos')
                      ->join('usuarios u', 'u.idusuario = ha.usuario_id', 'left')
                      ->join('personas p', 'p.idpersona = u.idpersona', 'left');

        // Aplicar filtros
        if (!empty($filtros['tabla'])) {
            $builder->where('ha.tabla_afectada', $filtros['tabla']);
        }
        
        if (!empty($filtros['accion'])) {
            $builder->where('ha.accion', $filtros['accion']);
        }
        
        if (!empty($filtros['usuario_id'])) {
            $builder->where('ha.usuario_id', $filtros['usuario_id']);
        }
        
        if (!empty($filtros['fecha_desde'])) {
            $builder->where('ha.created_at >=', $filtros['fecha_desde']);
        }
        
        if (!empty($filtros['fecha_hasta'])) {
            $builder->where('ha.created_at <=', $filtros['fecha_hasta']);
        }

        $actividades = $builder->orderBy('ha.created_at', 'DESC')
                              ->limit(100)
                              ->get()
                              ->getResultArray();

        // Obtener estadísticas del período (simplificado)
        $estadisticas = [
            'total_actividades' => count($actividades),
            'por_accion' => [],
            'por_usuario' => [],
            'por_dia' => []
        ];

        // Obtener reporte de productividad (simplificado)
        $reporte = [
            'completados_por_usuario' => [],
            'estados_actuales' => [],
            'trabajador_mas_activo' => null
        ];

        // Obtener usuarios para el filtro
        $db = \Config\Database::connect();
        $usuarios = $db->table('usuarios')
                       ->select('usuarios.idusuario, usuarios.nombreusuario, personas.nombres, personas.apellidos')
                       ->join('personas', 'personas.idpersona = usuarios.idpersona', 'left')
                       ->where('usuarios.estado', 1)
                       ->get()
                       ->getResultArray();

        $data = [
            'title' => 'Historial del Sistema',
            'actividades' => $actividades,
            'estadisticas' => $estadisticas,
            'reporte' => $reporte,
            'usuarios' => $usuarios,
            'filtros' => $filtros,
            'header' => view('Layouts/header'),
            'footer' => view('Layouts/footer')
        ];

        return view('historial/index', $data);
    }

    /**
     * Obtener actividades via AJAX con filtros
     */
    public function obtenerActividades()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Petición no válida']);
        }

        if (!$this->esAdministrador()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso denegado']);
        }

        try {
            $filtros = [
                'tabla' => $this->request->getPost('tabla'),
                'accion' => $this->request->getPost('accion'),
                'usuario_id' => $this->request->getPost('usuario_id'),
                'fecha_desde' => $this->request->getPost('fecha_desde'),
                'fecha_hasta' => $this->request->getPost('fecha_hasta')
            ];

            $limite = (int)($this->request->getPost('limite') ?? 50);
            
            $actividades = $this->historialModel->getActividadesRecientes($limite, $filtros);
            
            // Formatear actividades para la vista
            $actividadesFormateadas = array_map(function($actividad) {
                return [
                    'id' => $actividad['id'],
                    'descripcion' => $actividad['descripcion'],
                    'usuario' => $this->formatearNombreUsuario($actividad),
                    'fecha' => date('d/m/Y H:i', strtotime($actividad['created_at'])),
                    'fecha_relativa' => $this->formatearFechaRelativa($actividad['created_at']),
                    'accion' => $actividad['accion'],
                    'tabla' => $actividad['tabla_afectada'],
                    'registro_id' => $actividad['registro_id']
                ];
            }, $actividades);

            return $this->response->setJSON([
                'success' => true,
                'actividades' => $actividadesFormateadas,
                'total' => count($actividadesFormateadas)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error obteniendo actividades: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al obtener actividades'
            ]);
        }
    }

    /**
     * Exportar historial a CSV (solo administradores)
     */
    public function exportarCSV()
    {
        if (!$this->esAdministrador()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }

        try {
            $filtros = [
                'fecha_desde' => $this->request->getGet('fecha_desde') ?? date('Y-m-01'),
                'fecha_hasta' => $this->request->getGet('fecha_hasta') ?? date('Y-m-d')
            ];

            $actividades = $this->historialModel->getActividadesRecientes(1000, $filtros);

            // Configurar headers para descarga
            $filename = 'historial_' . date('Y-m-d_H-i-s') . '.csv';
            
            $this->response->setHeader('Content-Type', 'text/csv');
            $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');

            // Crear CSV
            $output = fopen('php://output', 'w');
            
            // Headers del CSV
            fputcsv($output, [
                'ID',
                'Fecha',
                'Usuario',
                'Acción',
                'Tabla',
                'Registro ID',
                'Descripción'
            ]);

            // Datos
            foreach ($actividades as $actividad) {
                fputcsv($output, [
                    $actividad['id'],
                    $actividad['created_at'],
                    $this->formatearNombreUsuario($actividad),
                    $actividad['accion'],
                    $actividad['tabla_afectada'],
                    $actividad['registro_id'],
                    $actividad['descripcion']
                ]);
            }

            fclose($output);
            return $this->response;

        } catch (\Exception $e) {
            log_message('error', 'Error exportando CSV: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al exportar el historial');
            return redirect()->to('historial');
        }
    }

    /**
     * Limpiar historial antiguo (solo administradores)
     */
    public function limpiarHistorial()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Petición no válida']);
        }

        if (!$this->esAdministrador()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Acceso denegado']);
        }

        try {
            $diasMantener = (int)($this->request->getPost('dias') ?? 365);
            
            if ($diasMantener < 30) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Debe mantener al menos 30 días de historial'
                ]);
            }

            $eliminados = $this->historialModel->limpiarHistorialAntiguo($diasMantener);

            return $this->response->setJSON([
                'success' => true,
                'message' => "Se eliminaron {$eliminados} registros antiguos del historial",
                'eliminados' => $eliminados
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error limpiando historial: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error al limpiar el historial'
            ]);
        }
    }

    /**
     * Verificar si el usuario actual es administrador
     */
    private function esAdministrador(): bool
    {
        $session = session();
        $tipoUsuario = $session->get('tipo_usuario');
        return $tipoUsuario === 'admin';
    }

    /**
     * Formatear nombre de usuario para mostrar
     */
    private function formatearNombreUsuario(array $actividad): string
    {
        if (!empty($actividad['nombres']) && !empty($actividad['apellidos'])) {
            $nombres = explode(' ', $actividad['nombres']);
            $apellidos = explode(' ', $actividad['apellidos']);
            return $nombres[0] . ' ' . $apellidos[0] . '.';
        }
        
        return $actividad['nombreusuario'] ?? 'Usuario';
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
}
