<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventarioEquipoModel;
use App\Models\CateEquipoModel;
use App\Models\MarcaEquipoModel;
use App\Models\UbicacionModel;

class InventarioController extends BaseController
{
    protected $equipoModel;
    protected $categoriaModel;
    protected $marcaModel;
    protected $ubicacionModel;

    public function __construct()
    {
        $this->equipoModel = new InventarioEquipoModel();
        $this->categoriaModel = new CateEquipoModel();
        $this->marcaModel = new MarcaEquipoModel();
        $this->ubicacionModel = new UbicacionModel();
    }

    /**
     * Mostrar listado de equipos
     */
    public function index()
    {
        try {
            $equipos = $this->equipoModel->getEquiposConDetalles();
            $estadisticas = $this->equipoModel->getEstadisticas();
            $categorias = $this->categoriaModel->getCategorias();
            $marcas = $this->marcaModel->getMarcas();

            $data = [
                'title' => 'Inventario de Equipos',
                'equipos' => $equipos,
                'estadisticas' => $estadisticas,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'header' => view('Layouts/header'),
                'footer' => view('Layouts/footer')
            ];

            return view('Inventario/listar', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::index: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar el inventario');
            return redirect()->back();
        }
    }

    /**
     * Mostrar formulario para crear nuevo equipo
     */
    public function create()
    {
        try {
            $categorias = $this->categoriaModel->getCategorias();
            $marcas = $this->marcaModel->getMarcas();
            $ubicaciones = $this->ubicacionModel->getUbicaciones();

            $data = [
                'title' => 'Agregar Nuevo Equipo',
                'categorias' => $categorias,
                'marcas' => $marcas,
                'ubicaciones' => $ubicaciones,
                'validation' => \Config\Services::validation(),
                'header' => view('Layouts/header'),
                'footer' => view('Layouts/footer')
            ];

            return view('Inventario/crear', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::create: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar el formulario');
            return redirect()->to('/inventario');
        }
    }

    /**
     * Guardar nuevo equipo
     */
    public function store()
    {
        try {
            // Validar datos
            if (!$this->validate($this->equipoModel->getValidationRules())) {
                return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
            }

            // Preparar datos
            $data = $this->request->getPost();
            $data['cantDisponible'] = (int)$data['cantDisponible'];
            
            // Manejar imagen
            $imagen = $this->request->getFile('imagen_file');
            if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
                $newName = $imagen->getRandomName();
                $imagen->move(ROOTPATH . 'public/uploads/equipos/', $newName);
                $data['imgEquipo'] = 'uploads/equipos/' . $newName;
            }

            // Guardar en base de datos
            if ($this->equipoModel->save($data)) {
                session()->setFlashdata('success', 'Equipo agregado exitosamente al inventario.');
                return redirect()->to('/inventario');
            } else {
                session()->setFlashdata('error', 'Error al guardar el equipo. Intente nuevamente.');
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::store: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error del servidor: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Mostrar formulario para editar equipo
     */
    public function edit($id = null)
    {
        try {
            if (!$id) {
                session()->setFlashdata('error', 'ID de equipo no válido.');
                return redirect()->to('/inventario');
            }

            $equipo = $this->equipoModel->getEquipoConDetalles($id);
            
            if (!$equipo) {
                session()->setFlashdata('error', 'Equipo no encontrado.');
                return redirect()->to('/inventario');
            }

            $categorias = $this->categoriaModel->getCategorias();
            $marcas = $this->marcaModel->getMarcas();
            $ubicaciones = $this->ubicacionModel->getUbicaciones();

            $data = [
                'title' => 'Editar Equipo',
                'equipo' => $equipo,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'ubicaciones' => $ubicaciones,
                'validation' => \Config\Services::validation(),
                'header' => view('Layouts/header'),
                'footer' => view('Layouts/footer')
            ];

            return view('Inventario/editar', $data);

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::edit: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al cargar el equipo');
            return redirect()->to('/inventario');
        }
    }

    /**
     * Actualizar equipo
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                session()->setFlashdata('error', 'ID de equipo no válido.');
                return redirect()->to('/inventario');
            }

            $equipoExistente = $this->equipoModel->find($id);
            if (!$equipoExistente) {
                session()->setFlashdata('error', 'Equipo no encontrado.');
                return redirect()->to('/inventario');
            }

            // Validar datos
            if (!$this->validate($this->equipoModel->getValidationRules())) {
                return redirect()->back()->withInput()->with('validation_errors', $this->validator->getErrors());
            }

            // Preparar datos
            $data = $this->request->getPost();
            $data['cantDisponible'] = (int)$data['cantDisponible'];

            // Manejar imagen
            $imagen = $this->request->getFile('imagen_file');
            if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
                // Eliminar imagen anterior si existe
                if (!empty($equipoExistente['imgEquipo']) && file_exists(ROOTPATH . 'public/' . $equipoExistente['imgEquipo'])) {
                    unlink(ROOTPATH . 'public/' . $equipoExistente['imgEquipo']);
                }
                
                $newName = $imagen->getRandomName();
                $imagen->move(ROOTPATH . 'public/uploads/equipos/', $newName);
                $data['imgEquipo'] = 'uploads/equipos/' . $newName;
            }

            // Actualizar en base de datos
            if ($this->equipoModel->update($id, $data)) {
                session()->setFlashdata('success', 'Equipo actualizado exitosamente.');
                return redirect()->to('/inventario');
            } else {
                session()->setFlashdata('error', 'Error al actualizar el equipo. Intente nuevamente.');
                return redirect()->back()->withInput();
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::update: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error del servidor: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Eliminar equipo
     */
    public function delete($id = null)
    {
        try {
            if (!$this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Solicitud no válida.'
                ]);
            }

            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de equipo no válido.'
                ]);
            }

            $equipo = $this->equipoModel->find($id);
            if (!$equipo) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Equipo no encontrado.'
                ]);
            }

            // Eliminar imagen si existe
            if (!empty($equipo['imgEquipo']) && file_exists(ROOTPATH . 'public/' . $equipo['imgEquipo'])) {
                unlink(ROOTPATH . 'public/' . $equipo['imgEquipo']);
            }

            // Eliminar de la base de datos
            if ($this->equipoModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Equipo eliminado exitosamente.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar el equipo.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::delete: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Buscar equipos
     */
    public function buscar()
    {
        try {
            $criterios = [
                'categoria' => $this->request->getGet('categoria'),
                'marca' => $this->request->getGet('marca'),
                'estado' => $this->request->getGet('estado'),
                'modelo' => $this->request->getGet('modelo')
            ];

            $equipos = $this->equipoModel->buscarEquipos($criterios);

            return $this->response->setJSON([
                'success' => true,
                'equipos' => $equipos
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::buscar: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error en la búsqueda'
            ]);
        }
    }

    /**
     * Ver detalles de equipo
     */
    public function ver($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de equipo no válido.'
                ]);
            }

            $equipo = $this->equipoModel->getEquipoConDetalles($id);
            
            if (!$equipo) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Equipo no encontrado.'
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'equipo' => $equipo
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::ver: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener los detalles'
            ]);
        }
    }

    /**
     * Obtener estadísticas
     */
    public function estadisticas()
    {
        try {
            $estadisticas = $this->equipoModel->getEstadisticas();

            return $this->response->setJSON([
                'success' => true,
                'estadisticas' => $estadisticas
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en InventarioController::estadisticas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener estadísticas'
            ]);
        }
    }
}