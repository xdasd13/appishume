<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\InventarioEquipoModel;
use App\Models\CateEquipoModel;
use App\Models\MarcaEquipoModel;
use App\Models\UbicacionModel;

/**
 * Controlador para la gestión del inventario de equipos audiovisuales
 */
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
     * Mostrar listado de equipos en formato de cards
     */
    public function index()
    {
        try {
            // Obtener todos los equipos con detalles
            $equipos = $this->equipoModel->getEquiposConDetalles();
            
            // Obtener estadísticas para el dashboard
            $estadisticas = $this->equipoModel->getEstadisticas();
            
            // Obtener categorías y marcas para filtros
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
            session()->setFlashdata('error', 'Error al cargar el inventario: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Mostrar formulario para crear nuevo equipo
     */
    public function create()
    {
        try {
            // Obtener datos para los selects
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
            session()->setFlashdata('error', 'Error al cargar el formulario: ' . $e->getMessage());
            return redirect()->to('/inventario');
        }
    }

    /**
     * Guardar nuevo equipo en la base de datos
     */
    public function store()
    {
        try {
            // Validar datos del formulario
            $rules = $this->equipoModel->getValidationRules();
            
            if (!$this->validate($rules)) {
                session()->setFlashdata('validation_errors', $this->validator->getErrors());
                return redirect()->back()->withInput();
            }

            // Preparar datos para insertar
            $data = [
                'idCateEquipo' => $this->request->getPost('idCateEquipo'),
                'idMarca' => $this->request->getPost('idMarca'),
                'modelo' => trim($this->request->getPost('modelo')),
                'descripcion' => trim($this->request->getPost('descripcion')),
                'caracteristica' => trim($this->request->getPost('caracteristica')),
                'sku' => trim($this->request->getPost('sku')),
                'numSerie' => trim($this->request->getPost('numSerie')),
                'cantDisponible' => (int)$this->request->getPost('cantDisponible'),
                'estado' => $this->request->getPost('estado'),
                'fechaCompra' => $this->request->getPost('fechaCompra') ?: null,
                'imgEquipo' => $this->request->getPost('imgEquipo')
            ];

            // Manejar subida de imagen si existe
            $imagen = $this->request->getFile('imagen_file');
            if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
                $nombreImagen = $imagen->getRandomName();
                $imagen->move(ROOTPATH . 'public/uploads/equipos/', $nombreImagen);
                $data['imgEquipo'] = 'uploads/equipos/' . $nombreImagen;
            }

            // Insertar en la base de datos
            $equipoId = $this->equipoModel->insert($data);

            if ($equipoId) {
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
     * Mostrar formulario para editar equipo existente
     */
    public function edit($id = null)
    {
        try {
            if (!$id) {
                session()->setFlashdata('error', 'ID de equipo no válido.');
                return redirect()->to('/inventario');
            }

            // Obtener datos del equipo
            $equipo = $this->equipoModel->getEquipoConDetalles($id);
            
            if (!$equipo) {
                session()->setFlashdata('error', 'Equipo no encontrado.');
                return redirect()->to('/inventario');
            }

            // Obtener datos para los selects
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
            session()->setFlashdata('error', 'Error al cargar el equipo: ' . $e->getMessage());
            return redirect()->to('/inventario');
        }
    }

    /**
     * Actualizar equipo en la base de datos
     */
    public function update($id = null)
    {
        try {
            if (!$id) {
                session()->setFlashdata('error', 'ID de equipo no válido.');
                return redirect()->to('/inventario');
            }

            // Verificar que el equipo existe
            $equipoExistente = $this->equipoModel->find($id);
            if (!$equipoExistente) {
                session()->setFlashdata('error', 'Equipo no encontrado.');
                return redirect()->to('/inventario');
            }

            // Validar datos del formulario
            $rules = $this->equipoModel->getValidationRules();
            
            if (!$this->validate($rules)) {
                session()->setFlashdata('validation_errors', $this->validator->getErrors());
                return redirect()->back()->withInput();
            }

            // Preparar datos para actualizar
            $data = [
                'idCateEquipo' => $this->request->getPost('idCateEquipo'),
                'idMarca' => $this->request->getPost('idMarca'),
                'modelo' => trim($this->request->getPost('modelo')),
                'descripcion' => trim($this->request->getPost('descripcion')),
                'caracteristica' => trim($this->request->getPost('caracteristica')),
                'sku' => trim($this->request->getPost('sku')),
                'numSerie' => trim($this->request->getPost('numSerie')),
                'cantDisponible' => (int)$this->request->getPost('cantDisponible'),
                'estado' => $this->request->getPost('estado'),
                'fechaCompra' => $this->request->getPost('fechaCompra') ?: null,
                'fechaUso' => $this->request->getPost('fechaUso') ?: null
            ];

            // Manejar subida de nueva imagen si existe
            $imagen = $this->request->getFile('imagen_file');
            if ($imagen && $imagen->isValid() && !$imagen->hasMoved()) {
                // Eliminar imagen anterior si existe
                if (!empty($equipoExistente['imgEquipo']) && file_exists(ROOTPATH . 'public/' . $equipoExistente['imgEquipo'])) {
                    unlink(ROOTPATH . 'public/' . $equipoExistente['imgEquipo']);
                }
                
                $nombreImagen = $imagen->getRandomName();
                $imagen->move(ROOTPATH . 'public/uploads/equipos/', $nombreImagen);
                $data['imgEquipo'] = 'uploads/equipos/' . $nombreImagen;
            } elseif ($this->request->getPost('imgEquipo')) {
                $data['imgEquipo'] = $this->request->getPost('imgEquipo');
            }

            // Actualizar en la base de datos
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
     * Eliminar equipo del inventario
     */
    public function delete($id = null)
    {
        try {
            if (!$id) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ID de equipo no válido.'
                ]);
            }

            // Verificar que el equipo existe
            $equipo = $this->equipoModel->find($id);
            if (!$equipo) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Equipo no encontrado.'
                ]);
            }

            // Eliminar imagen asociada si existe
            if (!empty($equipo['imgEquipo']) && file_exists(ROOTPATH . 'public/' . $equipo['imgEquipo'])) {
                unlink(ROOTPATH . 'public/' . $equipo['imgEquipo']);
            }

            // Eliminar de la base de datos
            if ($this->equipoModel->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Equipo eliminado exitosamente del inventario.'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar el equipo. Intente nuevamente.'
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
     * Buscar equipos con filtros (AJAX)
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
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ver detalles de un equipo específico (AJAX)
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
                'message' => 'Error al obtener los detalles: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener estadísticas del inventario (AJAX)
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
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ]);
        }
    }
}
