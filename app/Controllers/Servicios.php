<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ServicioModel;
use App\Models\EquipoModel;

class Servicios extends BaseController
{
    protected $servicioModel;
    protected $equipoModel;

    public function __construct()
    {
        $this->servicioModel = new ServicioModel();
        $this->equipoModel = new EquipoModel();
    }

    // Detalle de servicio con equipos asignados
    public function detalle($idserviciocontratado)
    {
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        $datos['servicio'] = $this->servicioModel->getServicioContratado($idserviciocontratado);
        $datos['equipos'] = $this->equipoModel->getEquiposPorServicio($idserviciocontratado);
        $datos['titulo'] = 'Detalle del Servicio';
        
        return view('servicios/detalle', $datos);
    }
}