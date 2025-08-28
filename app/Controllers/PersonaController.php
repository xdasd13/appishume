<?php

namespace App\Controllers;
use App\Controllers\BaseController;

class PersonaController extends BaseController{

  public function index(){
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    return view('Personas/index', $datos);
  }

  public function crear(){
    $datos['header'] = view('Layouts/header');
    $datos['footer'] = view('Layouts/footer');
    return view('Personas/crear', $datos);
  }

}