<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EquipoModel;
use App\Models\ServicioModel;
use App\Models\UsuarioModel;

class Equipos extends BaseController
{
    protected $equipoModel;
    protected $servicioModel;
    protected $usuarioModel;

    public function __construct()
    {
        $this->equipoModel = new EquipoModel();
        $this->servicioModel = new ServicioModel();
        $this->usuarioModel = new UsuarioModel();
    }

    // Listar todos los equipos
    public function index()
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipos'] = $this->equipoModel->getEquipos();
        $datos['titulo'] = 'Gestión de Equipos';
        
        return view('equipos/listar', $datos);
    }

    // Asignar equipo a un servicio
    public function asignar($idserviciocontratado)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['servicio'] = $this->servicioModel->getServicioContratado($idserviciocontratado);
        $datos['usuarios'] = $this->usuarioModel->getUsuarios();
        $datos['titulo'] = 'Asignar Equipo al Servicio';
        
        return view('equipos/asignar', $datos);
    }

    // Guardar asignación de equipo
    public function guardar()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'idusuario' => 'required',
            'descripcion' => 'required',
            'estadoservicio' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Por favor complete todos los campos requeridos.');
            return redirect()->back();
        }

        $data = [
            'idserviciocontratado' => $this->request->getPost('idserviciocontratado'),
            'idusuario' => $this->request->getPost('idusuario'),
            'descripcion' => $this->request->getPost('descripcion'),
            'estadoservicio' => $this->request->getPost('estadoservicio')
        ];

        if ($this->equipoModel->insertEquipo($data)) {
            session()->setFlashdata('success', 'Equipo asignado correctamente.');
        } else {
            session()->setFlashdata('error', 'Error al asignar el equipo.');
        }

        return redirect()->to('equipos/por-servicio/' . $data['idserviciocontratado']);
    }

    // Editar asignación de equipo
    public function editar($idequipo)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipo'] = $this->equipoModel->getEquipo($idequipo);
        $datos['usuarios'] = $this->usuarioModel->getUsuarios();
        $datos['titulo'] = 'Editar Asignación de Equipo';
        
        return view('equipos/editar', $datos);
    }

    // Actualizar asignación de equipo
    public function actualizar()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'idusuario' => 'required',
            'descripcion' => 'required',
            'estadoservicio' => 'required'
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', 'Por favor complete todos los campos requeridos.');
            return redirect()->back();
        }

        $idequipo = $this->request->getPost('idequipo');
        $data = [
            'idusuario' => $this->request->getPost('idusuario'),
            'descripcion' => $this->request->getPost('descripcion'),
            'estadoservicio' => $this->request->getPost('estadoservicio')
        ];

        if ($this->equipoModel->updateEquipo($idequipo, $data)) {
            session()->setFlashdata('success', 'Asignación actualizada correctamente.');
        } else {
            session()->setFlashdata('error', 'Error al actualizar la asignación.');
        }

        return redirect()->to('equipos');
    }

    // Ver equipos por servicio
    public function por_servicio($idserviciocontratado)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipos'] = $this->equipoModel->getEquiposPorServicio($idserviciocontratado);
        $datos['servicio'] = $this->servicioModel->getServicioContratado($idserviciocontratado);
        $datos['titulo'] = 'Equipos Asignados al Servicio';
        
        return view('equipos/listar', $datos);
    }

    // Ver equipos por usuario
    public function por_usuario($idusuario)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['equipos'] = $this->equipoModel->getEquiposPorUsuario($idusuario);
        $datos['usuario'] = $this->usuarioModel->getUsuario($idusuario);
        $datos['titulo'] = 'Equipos Asignados al Usuario';
        
        return view('equipos/listar', $datos);
    }
}