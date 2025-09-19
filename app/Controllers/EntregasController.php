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

    // Listado principal de contratos y entregas
    public function index()
    {
        $datos['contratos'] = $this->entregasModel->obtenerContratosConEstadoPago();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/listar', $datos);
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
            $datos['servicios'] = $this->entregasModel->obtenerServiciosPorContratoPagado($contratoId);
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
        $personaId = session()->get('persona_id'); // Añade esto a tu sesión al iniciar sesión

        $validation = \Config\Services::validation();
        $validation->setRules([
            'idcontrato' => 'required|numeric',
            'idserviciocontratado' => 'required|numeric|is_not_unique[servicioscontratados.idserviciocontratado]',
            'observaciones' => 'required|min_length[10]',
            'comprobante_entrega' => 'uploaded[comprobante_entrega]|max_size[comprobante_entrega,5120]|ext_in[comprobante_entrega,pdf]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
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
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => 'pendiente',
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

    public function pendientes()
    {
        $datos['entregas'] = $this->entregasModel->obtenerEntregasPendientes();
        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/pendientes', $datos);
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

    public function historial()
    {
        // Usa el método del modelo en lugar de intentar crear la consulta directamente
        $datos['entregas'] = $this->entregasModel->obtenerTodasLasEntregas();

        $datos['header'] = view('Layouts/header');
        $datos['footer'] = view('Layouts/footer');
        return view('entregas/completadas', $datos);
    }

    public function marcarCompletada($id)
    {
        $entrega = $this->entregasModel->find($id);
        if (!$entrega) {
            return redirect()->to('entregas/completadas')->with('error', 'Entrega no encontrada');
        }

        $data = [
            'estado' => 'completada',
            'fecha_real_entrega' => date('Y-m-d H:i:s')
        ];

        if ($this->entregasModel->update($id, $data)) {
            return redirect()->to('entregas/ver/' . $id)->with('success', 'Entrega marcada como completada');
        } else {
            return redirect()->to('entregas/ver/' . $id)->with('error', 'Error al actualizar el estado de la entrega');
        }
    }
}