<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntregasModel;
use App\Models\ServiciosContratadosModel;
use App\Models\PersonasModel;

class EntregasController extends BaseController
{
    protected $entregasModel;
    protected $serviciosContratadosModel;
    protected $personasModel;
    protected $db;

    public function __construct()
    {
        $this->entregasModel = new EntregasModel();
        $this->serviciosContratadosModel = new ServiciosContratadosModel();
        $this->personasModel = new PersonasModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $datos['entregas'] = $this->entregasModel->obtenerEntregasCompletas();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('/entregas/index', $datos);
    }

    public function ver($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return redirect()->to('/entregas')->with('error', 'ID de entrega inválido');
        }

        $entrega = $this->entregasModel->obtenerEntregaCompleta($id);

        if (!$entrega) {
            return redirect()->to('/entregas')->with('error', 'Entrega no encontrada');
        }

        $datos['entrega'] = $entrega;
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');

        return view('/entregas/ver', $datos);
    }

    public function crear()
    {
        // Obtener servicios contratados con sus fechas
        $builder = $this->db->table('servicioscontratados sc');
        $builder->select('sc.idserviciocontratado, sc.fechahoraservicio, s.servicio as servicio_nombre');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $builder->where('sc.fechahoraservicio <=', date('Y-m-d H:i:s')); // Solo servicios ya realizados
        $datos['servicios'] = $builder->get()->getResultArray();

        // Solo obtener personas que pueden realizar entregas (empleados)
        $datos['personas'] = $this->personasModel
            ->join('usuarios u', 'u.idpersona = personas.idpersona', 'left')
            ->where('u.idusuario IS NOT NULL')
            ->findAll();

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');

        return view('/entregas/crear', $datos);
    }

    public function guardar()
    {
        $validation = \Config\Services::validation();

        // Reglas básicas
        $validation->setRules([
            'idserviciocontratado' => 'required|numeric|is_not_unique[servicioscontratados.idserviciocontratado]',
            'idpersona' => 'required|numeric|is_not_unique[personas.idpersona]',
            'fechahoraentrega' => 'required|valid_date',
            'estado_entrega' => 'required|in_list[pendiente,completada]',
            'observaciones' => 'required|min_length[10]'
        ], [
            'observaciones' => [
                'required' => 'El formato de entrega es obligatorio',
                'min_length' => 'El formato de entrega debe tener al menos 10 caracteres'
            ],
            'estado_entrega' => [
                'required' => 'El estado de la entrega es obligatorio',
                'in_list' => 'El estado debe ser Pendiente o Completada'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // VALIDACIÓN MANUAL de las 3 semanas
        $servicioId = $this->request->getPost('idserviciocontratado');
        $servicioContratado = $this->serviciosContratadosModel->find($servicioId);

        if (!$servicioContratado || !$servicioContratado['fechahoraservicio']) {
            return redirect()->back()->withInput()->with('error', 'El servicio seleccionado no es válido');
        }

        $fechaEntrega = strtotime($this->request->getPost('fechahoraentrega'));
        $fechaServicio = strtotime($servicioContratado['fechahoraservicio']);
        $fechaMaxima = strtotime('+3 weeks', $fechaServicio);

        // Validar que sea día hábil (lunes a viernes)
        $diaEntrega = date('N', $fechaEntrega);
        $esDiaHabil = ($diaEntrega >= 1 && $diaEntrega <= 5);

        // Validar horario laboral (8am - 6pm)
        $horaEntrega = date('H', $fechaEntrega);
        $enHorarioLaboral = ($horaEntrega >= 8 && $horaEntrega <= 18);

        if ($fechaEntrega <= $fechaServicio) {
            return redirect()->back()->withInput()->with('error', 'La fecha de entrega debe ser posterior al servicio');
        }

        if ($fechaEntrega > $fechaMaxima) {
            return redirect()->back()->withInput()->with('error', 'La entrega no puede ser más de 3 semanas después del servicio');
        }

        if (!$esDiaHabil) {
            return redirect()->back()->withInput()->with('error', 'Solo se permiten entregas en días hábiles (Lunes a Viernes)');
        }

        if (!$enHorarioLaboral) {
            return redirect()->back()->withInput()->with('error', 'Solo se permiten entregas en horario laboral (8:00 AM - 6:00 PM)');
        }

        // Validación adicional para estado "completada"
        $estado = $this->request->getPost('estado_entrega');
        $fechaReal = $this->request->getPost('fecha_real_entrega');

        if ($estado == 'completada') {
            if (empty($fechaReal)) {
                return redirect()->back()->withInput()->with('error', 'La fecha real de entrega es obligatoria para entregas completadas');
            }

            $fechaRealTimestamp = strtotime($fechaReal);
            if ($fechaRealTimestamp > time()) {
                return redirect()->back()->withInput()->with('error', 'La fecha real de entrega no puede ser futura');
            }
        }

        // Verificar que la persona existe
        $persona = $this->personasModel->find($this->request->getPost('idpersona'));
        if (!$persona) {
            return redirect()->back()->withInput()->with('error', 'La persona seleccionada no existe');
        }

        // Verificar que no exista ya una entrega para este servicio
        $entregaExistente = $this->entregasModel
            ->where('idserviciocontratado', $this->request->getPost('idserviciocontratado'))
            ->first();

        if ($entregaExistente) {
            return redirect()->back()->withInput()->with('error', 'Ya existe una entrega registrada para este servicio contratado');
        }

        $data = [
            'idserviciocontratado' => $this->request->getPost('idserviciocontratado'),
            'idpersona' => $this->request->getPost('idpersona'),
            'fechahoraentrega' => $this->request->getPost('fechahoraentrega'),
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => $estado,
            'fecha_real_entrega' => $estado == 'completada' ? $fechaReal : null
        ];

        // Usar transacción
        $this->db->transStart();

        try {
            if ($this->entregasModel->save($data)) {
                // Si se marca como completada, registrar en tabla de completados
                if ($estado == 'completada') {
                    $this->registrarEntregaCompletada($this->entregasModel->getInsertID());
                }

                $this->db->transCommit();
                return redirect()->to('/entregas')->with('success', 'Entrega registrada correctamente');
            } else {
                $this->db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Error al registrar la entrega');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error al guardar entrega: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error interno del sistema al registrar la entrega');
        }
    }

    public function pendientes()
    {
        $datos['entregas'] = $this->entregasModel->obtenerEntregasPendientes();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('/entregas/pendientes', $datos);
    }

    public function editar($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return redirect()->to('/entregas')->with('error', 'ID de entrega inválido');
        }

        $entrega = $this->entregasModel->find($id);

        if (!$entrega) {
            return redirect()->to('/entregas')->with('error', 'Entrega no encontrada');
        }

        // Obtener servicios contratados con sus fechas
        $builder = $this->db->table('servicioscontratados sc');
        $builder->select('sc.idserviciocontratado, sc.fechahoraservicio, s.servicio as servicio_nombre');
        $builder->join('servicios s', 's.idservicio = sc.idservicio');
        $datos['servicios'] = $builder->get()->getResultArray();

        // Solo obtener personas que pueden realizar entregas
        $datos['personas'] = $this->personasModel
            ->join('usuarios u', 'u.idpersona = personas.idpersona', 'left')
            ->where('u.idusuario IS NOT NULL')
            ->findAll();

        $datos['entrega'] = $entrega;
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');

        return view('/entregas/editar', $datos);
    }

    public function actualizar($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return redirect()->to('/entregas')->with('error', 'ID de entrega inválido');
        }

        $entregaExistente = $this->entregasModel->find($id);
        if (!$entregaExistente) {
            return redirect()->to('/entregas')->with('error', 'Entrega no encontrada');
        }

        $validation = \Config\Services::validation();

        // Reglas básicas
        $validation->setRules([
            'idserviciocontratado' => 'required|numeric|is_not_unique[servicioscontratados.idserviciocontratado]',
            'idpersona' => 'required|numeric|is_not_unique[personas.idpersona]',
            'fechahoraentrega' => 'required|valid_date',
            'estado' => 'required|in_list[pendiente,completada]',
            'observaciones' => 'required|min_length[10]'
        ], [
            'observaciones' => [
                'required' => 'El formato de entrega es obligatorio',
                'min_length' => 'El formato de entrega debe tener al menos 10 caracteres'
            ],
            'estado' => [
                'required' => 'El estado de la entrega es obligatorio',
                'in_list' => 'El estado debe ser Pendiente o Completada'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // VALIDACIÓN MANUAL de las 3 semanas
        $servicioId = $this->request->getPost('idserviciocontratado');
        $servicioContratado = $this->serviciosContratadosModel->find($servicioId);

        if (!$servicioContratado || !$servicioContratado['fechahoraservicio']) {
            return redirect()->back()->withInput()->with('error', 'El servicio seleccionado no es válido');
        }

        $fechaEntrega = strtotime($this->request->getPost('fechahoraentrega'));
        $fechaServicio = strtotime($servicioContratado['fechahoraservicio']);
        $fechaMaxima = strtotime('+3 weeks', $fechaServicio);

        // Validar que sea día hábil (lunes a viernes)
        $diaEntrega = date('N', $fechaEntrega);
        $esDiaHabil = ($diaEntrega >= 1 && $diaEntrega <= 5);

        // Validar horario laboral (8am - 6pm)
        $horaEntrega = date('H', $fechaEntrega);
        $enHorarioLaboral = ($horaEntrega >= 8 && $horaEntrega <= 18);

        if ($fechaEntrega <= $fechaServicio) {
            return redirect()->back()->withInput()->with('error', 'La fecha de entrega debe ser posterior al servicio');
        }

        if ($fechaEntrega > $fechaMaxima) {
            return redirect()->back()->withInput()->with('error', 'La entrega no puede ser más de 3 semanas después del servicio');
        }

        if (!$esDiaHabil) {
            return redirect()->back()->withInput()->with('error', 'Solo se permiten entregas en días hábiles (Lunes a Viernes)');
        }

        if (!$enHorarioLaboral) {
            return redirect()->back()->withInput()->with('error', 'Solo se permiten entregas en horario laboral (8:00 AM - 6:00 PM)');
        }

        // Validación adicional para estado "completada"
        $estado = $this->request->getPost('estado');
        $fechaReal = $this->request->getPost('fecha_real_entrega');

        if ($estado == 'completada') {
            if (empty($fechaReal)) {
                return redirect()->back()->withInput()->with('error', 'La fecha real de entrega es obligatoria para entregas completadas');
            }

            $fechaRealTimestamp = strtotime($fechaReal);
            if ($fechaRealTimestamp > time()) {
                return redirect()->back()->withInput()->with('error', 'La fecha real de entrega no puede ser futura');
            }
        }

        // Verificar que la persona existe
        $persona = $this->personasModel->find($this->request->getPost('idpersona'));
        if (!$persona) {
            return redirect()->back()->withInput()->with('error', 'La persona seleccionada no existe');
        }

        // Verificar que no exista ya otra entrega para este servicio (excepto la actual)
        $entregaDuplicada = $this->entregasModel
            ->where('idserviciocontratado', $this->request->getPost('idserviciocontratado'))
            ->where('identregable !=', $id)
            ->first();

        if ($entregaDuplicada) {
            return redirect()->back()->withInput()->with('error', 'Ya existe otra entrega registrada para este servicio contratado');
        }

        $data = [
            'idserviciocontratado' => $this->request->getPost('idserviciocontratado'),
            'idpersona' => $this->request->getPost('idpersona'),
            'fechahoraentrega' => $this->request->getPost('fechahoraentrega'),
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => $estado,
            'fecha_real_entrega' => $estado == 'completada' ? $fechaReal : null
        ];

        // Usar transacción
        $this->db->transStart();

        try {
            $estadoAnterior = $entregaExistente['estado'];
            $nuevoEstado = $estado;

            if ($this->entregasModel->update($id, $data)) {
                // Si se cambió de pendiente a completada, registrar en tabla de completados
                if ($estadoAnterior == 'pendiente' && $nuevoEstado == 'completada') {
                    $this->registrarEntregaCompletada($id);
                }

                $this->db->transCommit();
                return redirect()->to('/entregas/ver/' . $id)->with('success', 'Entrega actualizada correctamente');
            } else {
                $this->db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Error al actualizar la entrega');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error al actualizar entrega: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error interno del sistema al actualizar la entrega');
        }
    }

    public function eliminar($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return redirect()->to('/entregas')->with('error', 'ID de entrega inválido');
        }

        $entrega = $this->entregasModel->find($id);

        if (!$entrega) {
            return redirect()->to('/entregas')->with('error', 'Entrega no encontrada');
        }

        // Usar transacción
        $this->db->transStart();

        try {
            if ($this->entregasModel->delete($id)) {
                // También eliminar de la tabla de completados si existe
                $this->db->table('entregas_completadas')->where('identregable', $id)->delete();

                $this->db->transCommit();
                return redirect()->to('/entregas')->with('success', 'Entrega eliminada correctamente');
            } else {
                $this->db->transRollback();
                return redirect()->to('/entregas')->with('error', 'Error al eliminar la entrega');
            }
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error al eliminar entrega: ' . $e->getMessage());
            return redirect()->to('/entregas')->with('error', 'Error interno del sistema al eliminar la entrega');
        }
    }

    /**
     * Registrar entrega en tabla de completados
     */
    private function registrarEntregaCompletada($idEntrega)
    {
        $entrega = $this->entregasModel->find($idEntrega);
        
        if ($entrega) {
            $dataCompletada = [
                'identregable' => $idEntrega,
                'fecha_completada' => date('Y-m-d H:i:s'),
                'observaciones' => 'Entrega completada: ' . ($entrega['observaciones'] ?? 'Sin observaciones')
            ];

            // Insertar en tabla de completados
            $this->db->table('entregas_completadas')->insert($dataCompletada);
        }
    }

    /**
     * Método para ver entregas completadas
     */
    public function completadas()
    {
        $datos['entregas'] = $this->entregasModel->obtenerEntregasCompletadas();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('/entregas/completadas', $datos);
    }

    /**
     * Método para ver entregas vencidas
     */
    public function vencidas()
    {
        $datos['entregas'] = $this->entregasModel->obtenerEntregasVencidas();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('/entregas/vencidas', $datos);
    }
}