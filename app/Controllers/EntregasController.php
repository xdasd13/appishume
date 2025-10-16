<?php

namespace App\Controllers;

use App\Models\EntregasModel;

class EntregasController extends BaseController
{
    protected $entregasModel;

    public function __construct()
    {
        $this->entregasModel = new EntregasModel();
    }

    // Listado principal de contratos y entregas - CORREGIDO
    public function index()
    {
        $datos['contratos'] = $this->entregasModel->obtenerContratosConEstadoPago();
        
        // Calcular estadísticas para las tarjetas
        $datos['estadisticas'] = $this->calcularEstadisticasEntregas($datos['contratos']);
        
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/listar', $datos);
    }

    // Método para calcular estadísticas de entregas
    private function calcularEstadisticasEntregas($contratos)
    {
        $estadisticas = [
            'total_contratos' => count($contratos),
            'contratos_pagados' => 0,
            'pendientes_entrega' => 0,
            'entregas_completadas' => 0,
            'total_servicios' => 0,
            'servicios_entregados' => 0
        ];

        foreach ($contratos as $contrato) {
            // Contar contratos pagados
            if (($contrato['deuda_actual'] ?? 0) <= 0.01) {
                $estadisticas['contratos_pagados']++;
            }
            
            // Contar entregas pendientes
            $totalServicios = intval($contrato['total_servicios'] ?? 0);
            $entregasCompletadas = intval($contrato['entregas_completadas'] ?? 0);
            $entregasPendientes = $totalServicios - $entregasCompletadas;
            
            if ($entregasPendientes > 0) {
                $estadisticas['pendientes_entrega']++;
            }
            
            // Acumular totales
            $estadisticas['total_servicios'] += $totalServicios;
            $estadisticas['servicios_entregados'] += $entregasCompletadas;
            
            // Si todos los servicios están entregados
            if ($totalServicios > 0 && $totalServicios == $entregasCompletadas) {
                $estadisticas['entregas_completadas']++;
            }
        }

        return $estadisticas;
    }

    // Página para crear nueva entrega (solo contratos pagados)
    public function crear()
    {
        $contratoId = $this->request->getGet('contrato');

        // Si se especificó un contrato, verificar que esté completamente pagado
        if ($contratoId) {
            // Verificar que el contrato existe y está pagado
            $contrato = $this->entregasModel->obtenerContratoPagado($contratoId);

            if (!$contrato) {
                return redirect()->to('entregas')->with('error', 'El contrato seleccionado no existe o no está pagado completamente.');
            }

            // Obtener servicios disponibles para este contrato
            $servicios = $this->entregasModel->obtenerServiciosPorContratoPagado($contratoId);
            
            // Si no hay servicios disponibles, significa que todas las entregas ya fueron registradas
            if (empty($servicios)) {
                return redirect()->to('entregas')->with('info', 'Todas las entregas de este contrato ya han sido registradas.');
            }
            
            $datos['servicios'] = $servicios;
            $datos['contrato'] = $contrato;
            $datos['usuario_actual'] = session()->get('usuario_nombre');
            $datos['usuario_id'] = session()->get('usuario_id');
            $datos['header'] = view('Layouts/header');
            $datos['footer'] = view('Layouts/footer');
            return view('entregas/crear', $datos);
        }

        // Si no se especificó contrato, mostrar listado de contratos pagados
        $datos['contratos'] = $this->entregasModel->obtenerContratosPagadosCompletos();
        $datos['usuario_actual'] = session()->get('usuario_nombre');
        $datos['usuario_id'] = session()->get('usuario_id');
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/crear', $datos);
    }

    // AJAX: obtener servicios de un contrato pagado
    public function obtenerServiciosPorContrato($idcontrato)
    {
        $servicios = $this->entregasModel->obtenerServiciosPorContratoPagado($idcontrato);
        return $this->response->setJSON(['success' => true, 'servicios' => $servicios]);
    }

    // Guardar nueva entrega
    public function guardar()
    {
        // Importante: Asegúrate de que obtienes el ID de persona, no el ID de usuario
        $usuarioId = session()->get('usuario_id');
        
        // Obtener el persona_id desde la tabla usuarios
        $personaId = null;
        if ($usuarioId) {
            $db = db_connect();
            $query = $db->query("SELECT idpersona FROM usuarios WHERE idusuario = ?", [$usuarioId]);
            $result = $query->getRowArray();
            $personaId = $result['idpersona'] ?? null;
        }

        // Validar que se haya seleccionado un contrato
        $idcontrato = $this->request->getPost('idcontrato');
        if (empty($idcontrato)) {
            return redirect()->back()->withInput()->with('error', 'Debe seleccionar un contrato pagado para registrar la entrega.');
        }

        // Verificar que el contrato esté pagado
        $contrato = $this->entregasModel->obtenerContratoPagado($idcontrato);
        if (!$contrato) {
            return redirect()->back()->withInput()->with('error', 'El contrato seleccionado no existe o no está pagado completamente. Solo se pueden registrar entregas para contratos pagados al 100%.');
        }

        // Validar los datos del formulario
        $reglas = [
            'idcontrato' => 'required|numeric',
            'idserviciocontratado' => 'required|numeric|is_not_unique[servicioscontratados.idserviciocontratado]',
            'observaciones' => 'required|min_length[10]',
            'comprobante_entrega' => 'uploaded[comprobante_entrega]|max_size[comprobante_entrega,5120]|ext_in[comprobante_entrega,pdf]'
        ];

        if (!$this->validate($reglas)) {
            $errors = $this->validator->getErrors();
            $errorMessage = 'Por favor, corrija los siguientes errores:\n\n';
            foreach ($errors as $field => $error) {
                $errorMessage .= '• ' . $error . '\n';
            }
            return redirect()->back()->withInput()->with('error', $errorMessage);
        }

        $comprobante = $this->request->getFile('comprobante_entrega');
        $nombreComprobante = null;
        if ($comprobante && $comprobante->isValid() && !$comprobante->hasMoved()) {
            $nuevoNombre = $comprobante->getRandomName();
            $comprobante->move(ROOTPATH . 'public/uploads/comprobantes_entrega', $nuevoNombre);
            $nombreComprobante = $nuevoNombre;
        }

        // Usamos la fecha actual para la entrega
        $fechaActual = date('Y-m-d H:i:s');

        // IMPORTANTE: Verifica qué ID estás usando y asegúrate que sea coherente
        // Si tu relación es con personas, necesitas el ID de persona, no el ID de usuario
        $data = [
            'idserviciocontratado' => $this->request->getPost('idserviciocontratado'),
            'idpersona' => $personaId, // Usa persona_id en lugar de usuario_id
            'fechahoraentrega' => $fechaActual,
            'fecha_real_entrega' => $fechaActual, // Marcar como entregada al momento de registro
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => 'completada', // Cambiar a completada al registrar
            'comprobante_entrega' => $nombreComprobante
        ];

        if ($this->entregasModel->save($data)) {
            return redirect()->to('entregas')->with('success', 'Entrega registrada correctamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al registrar la entrega');
        }
    }

    public function editar($id)
    {
        $entrega = $this->entregasModel->obtenerEntregaCompleta($id);
        if (!$entrega) {
            return redirect()->to('entregas')->with('error', 'Entrega no encontrada');
        }
        $datos['entrega'] = $entrega;
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/editar', $datos);
    }

    public function actualizar($id)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'observaciones' => 'required|min_length[10]',
            'comprobante_entrega' => 'if_exist|max_size[comprobante_entrega,5120]|ext_in[comprobante_entrega,pdf]'
        ]);
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        $entrega = $this->entregasModel->find($id);
        if (!$entrega) {
            return redirect()->to('entregas')->with('error', 'Entrega no encontrada');
        }
        $comprobante = $this->request->getFile('comprobante_entrega');
        $nombreComprobante = $entrega['comprobante_entrega'];
        if ($comprobante && $comprobante->isValid() && !$comprobante->hasMoved()) {
            $nuevoNombre = $comprobante->getRandomName();
            $comprobante->move(ROOTPATH . 'public/uploads/comprobantes_entrega', $nuevoNombre);
            $nombreComprobante = $nuevoNombre;
        }
        $data = [
            'observaciones' => $this->request->getPost('observaciones'),
            'comprobante_entrega' => $nombreComprobante
        ];
        if ($this->entregasModel->update($id, $data)) {
            return redirect()->to('entregas')->with('success', 'Entrega actualizada correctamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar la entrega');
        }
    }

    public function eliminar($id)
    {
        if ($this->entregasModel->delete($id)) {
            return redirect()->to('entregas')->with('success', 'Entrega eliminada correctamente');
        } else {
            return redirect()->to('entregas')->with('error', 'No se pudo eliminar la entrega');
        }
    }


    public function ver($id)
    {
        $entrega = $this->entregasModel->obtenerEntregaCompleta($id);

        // Si no se encuentra la entrega, intentamos al menos mostrar datos básicos
        if (!$entrega) {
            // Intenta obtener al menos los datos básicos de la entrega
            $entregaBasica = $this->entregasModel->find($id);

            if (!$entregaBasica) {
                return redirect()->to('entregas/historial')->with('error', 'Entrega no encontrada');
            }

            $entrega = $entregaBasica;
            $entrega['nombre_cliente'] = 'Información no disponible';
            $entrega['apellido_cliente'] = '';
            $entrega['servicio'] = 'Información no disponible';
            $entrega['nombre_entrega'] = 'Información no disponible';
            $entrega['apellido_entrega'] = '';
            $entrega['tipodoc'] = '';
            $entrega['numerodoc'] = '';
            $entrega['telprincipal'] = '';
            $entrega['direccion'] = '';
            $entrega['descripcion_servicio'] = 'Información no disponible';
            $entrega['tipodoc_entrega'] = '';
            $entrega['numerodoc_entrega'] = '';

            // Agregar el estado_visual que falta
            if ($entrega['estado'] == 'completada') {
                $entrega['estado_visual'] = "✅ ENTREGADO";
            } else if ($entrega['estado'] == 'pendiente') {
                $entrega['estado_visual'] = "⏳ EN POSTPRODUCCIÓN";
            } else {
                $entrega['estado_visual'] = "❓ DESCONOCIDO";
            }
        }

        $datos['entrega'] = $entrega;
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/ver', $datos);
    }

    public function imprimir($id)
    {
        $entrega = $this->entregasModel->obtenerEntregaCompleta($id);

        // Si no se encuentra la entrega, intentamos al menos mostrar datos básicos
        if (!$entrega) {
            // Intenta obtener al menos los datos básicos de la entrega
            $entregaBasica = $this->entregasModel->find($id);

            if (!$entregaBasica) {
                return redirect()->to('entregas/historial')->with('error', 'Entrega no encontrada');
            }

            $entrega = $entregaBasica;
            $entrega['nombre_cliente'] = 'Información no disponible';
            $entrega['apellido_cliente'] = '';
            $entrega['servicio'] = 'Información no disponible';
            $entrega['nombre_entrega'] = 'Información no disponible';
            $entrega['apellido_entrega'] = '';
            $entrega['tipodoc'] = '';
            $entrega['numerodoc'] = '';
            $entrega['telprincipal'] = '';
            $entrega['direccion'] = '';
            $entrega['descripcion_servicio'] = 'Información no disponible';
            $entrega['tipodoc_entrega'] = '';
            $entrega['numerodoc_entrega'] = '';

            // Agregar el estado_visual que falta
            if ($entrega['estado'] == 'completada') {
                $entrega['estado_visual'] = "✅ ENTREGADO";
            } else if ($entrega['estado'] == 'pendiente') {
                $entrega['estado_visual'] = "⏳ EN POSTPRODUCCIÓN";
            } else {
                $entrega['estado_visual'] = "❓ DESCONOCIDO";
            }
        }

        $datos['entrega'] = $entrega;
        return view('entregas/imprimir', $datos);
    }

    public function historial()
    {
        // Actualizar entregas existentes sin responsable (solo la primera vez)
        $this->entregasModel->actualizarEntregasSinResponsable();
        
        // Usa el método del modelo en lugar de intentar crear la consulta directamente
        $datos['entregas'] = $this->entregasModel->obtenerTodasLasEntregas();

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/completadas', $datos);
    }


}