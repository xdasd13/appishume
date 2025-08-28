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
        // Solo obtener servicios contratados activos y válidos
        $datos['servicios'] = $this->serviciosContratadosModel
            ->where('fechahoraservicio >=', date('Y-m-d H:i:s'))
            ->orWhere('fechahoraservicio IS NULL')
            ->findAll();
            
        // Solo obtener personas que pueden realizar entregas (ej: empleados)
        $datos['personas'] = $this->personasModel
            ->join('usuarios u', 'u.idpersona = personas.idpersona', 'left')
            ->where('u.idusuario IS NOT NULL') // Solo personas que son usuarios del sistema
            ->findAll();
            
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        
        return view('/entregas/crear', $datos);
    }

    public function guardar()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'idserviciocontratado' => 'required|numeric|is_not_unique[servicioscontratados.idserviciocontratado]',
            'idpersona' => 'required|numeric|is_not_unique[personas.idpersona]',
            'fechahoraentrega' => 'required|valid_date|fecha_realista'
        ], [
            'fechahoraentrega' => [
                'fecha_realista' => 'La fecha de entrega debe ser realista y posterior a la fecha actual'
            ]
        ]);

        // Validación personalizada para fecha realista
        $validation->setRule('fechahoraentrega', 'Fecha de entrega', function($value) {
            $fechaEntrega = strtotime($value);
            $fechaActual = strtotime('now');
            $fechaMaxima = strtotime('+2 years'); // No permitir fechas más allá de 2 años
            
            return $fechaEntrega > $fechaActual && $fechaEntrega <= $fechaMaxima;
        });

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Verificar que el servicio contratado existe y es válido
        $servicioContratado = $this->serviciosContratadosModel->find($this->request->getPost('idserviciocontratado'));
        if (!$servicioContratado) {
            return redirect()->back()->withInput()->with('error', 'El servicio contratado seleccionado no existe');
        }

        // Verificar que la fecha de entrega es posterior a la fecha del servicio
        $fechaEntrega = strtotime($this->request->getPost('fechahoraentrega'));
        $fechaServicio = strtotime($servicioContratado['fechahoraservicio']);
        
        if ($fechaEntrega < $fechaServicio) {
            return redirect()->back()->withInput()->with('error', 'La fecha de entrega no puede ser anterior a la fecha del servicio');
        }

        // Verificar que la persona existe y puede realizar entregas
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
            'observaciones' => $this->request->getPost('observaciones')
        ];

        // Usar transacción para garantizar la integridad de los datos
        $this->db->transStart();
        
        try {
            if ($this->entregasModel->save($data)) {
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

    public function editar($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return redirect()->to('/entregas')->with('error', 'ID de entrega inválido');
        }

        $entrega = $this->entregasModel->find($id);
        
        if (!$entrega) {
            return redirect()->to('/entregas')->with('error', 'Entrega no encontrada');
        }

        // Solo obtener servicios contratados activos y válidos
        $datos['servicios'] = $this->serviciosContratadosModel
            ->where('fechahoraservicio >=', date('Y-m-d H:i:s'))
            ->orWhere('fechahoraservicio IS NULL')
            ->findAll();
            
        // Solo obtener personas que pueden realizar entregas (ej: empleados)
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
        
        $validation->setRules([
            'idserviciocontratado' => 'required|numeric|is_not_unique[servicioscontratados.idserviciocontratado]',
            'idpersona' => 'required|numeric|is_not_unique[personas.idpersona]',
            'fechahoraentrega' => 'required|valid_date|fecha_realista'
        ], [
            'fechahoraentrega' => [
                'fecha_realista' => 'La fecha de entrega debe ser realista y posterior a la fecha actual'
            ]
        ]);

        // Validación personalizada para fecha realista
        $validation->setRule('fechahoraentrega', 'Fecha de entrega', function($value) {
            $fechaEntrega = strtotime($value);
            $fechaActual = strtotime('now');
            $fechaMaxima = strtotime('+2 years');
            
            return $fechaEntrega > $fechaActual && $fechaEntrega <= $fechaMaxima;
        });

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Verificar que el servicio contratado existe
        $servicioContratado = $this->serviciosContratadosModel->find($this->request->getPost('idserviciocontratado'));
        if (!$servicioContratado) {
            return redirect()->back()->withInput()->with('error', 'El servicio contratado seleccionado no existe');
        }

        // Verificar que la fecha de entrega es posterior a la fecha del servicio
        $fechaEntrega = strtotime($this->request->getPost('fechahoraentrega'));
        $fechaServicio = strtotime($servicioContratado['fechahoraservicio']);
        
        if ($fechaEntrega < $fechaServicio) {
            return redirect()->back()->withInput()->with('error', 'La fecha de entrega no puede ser anterior a la fecha del servicio');
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
            'observaciones' => $this->request->getPost('observaciones')
        ];

        // Usar transacción
        $this->db->transStart();
        
        try {
            if ($this->entregasModel->update($id, $data)) {
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
}