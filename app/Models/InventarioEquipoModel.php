<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarioEquipoModel extends Model
{
    protected $table = 'equipo';
    protected $primaryKey = 'idEquipo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'idCateEquipo',
        'idMarca', 
        'modelo',
        'descripcion',
        'caracteristica',
        'sku',
        'numSerie',
        'cantDisponible',
        'estado',
        'fechaCompra',
        'fechaUso',
        'imgEquipo'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'idCateEquipo' => 'required|integer|greater_than[0]',
        'idMarca' => 'required|integer|greater_than[0]',
        'modelo' => 'required|string|min_length[2]|max_length[70]',
        'descripcion' => 'permit_empty|string|max_length[255]',
        'caracteristica' => 'permit_empty|string',
        'sku' => 'permit_empty|string|max_length[50]|is_unique[equipo.sku,idEquipo,{idEquipo}]',
        'numSerie' => 'permit_empty|string|max_length[100]|is_unique[equipo.numSerie,idEquipo,{idEquipo}]',
        'cantDisponible' => 'required|integer|greater_than_equal_to[0]',
        'estado' => 'required|in_list[Nuevo,EnUso,EnMantenimiento,Dañado,Otro]',
        'fechaCompra' => 'permit_empty|valid_date[Y-m-d]',
        'fechaUso' => 'permit_empty|valid_date[Y-m-d]',
        'imgEquipo' => 'permit_empty|string|max_length[255]'
    ];

    protected $validationMessages = [
        'idCateEquipo' => [
            'required' => 'La categoría es obligatoria.',
            'integer' => 'La categoría debe ser un número válido.',
            'greater_than' => 'Debe seleccionar una categoría válida.'
        ],
        'idMarca' => [
            'required' => 'La marca es obligatoria.',
            'integer' => 'La marca debe ser un número válido.',
            'greater_than' => 'Debe seleccionar una marca válida.'
        ],
        'modelo' => [
            'required' => 'El modelo es obligatorio.',
            'min_length' => 'El modelo debe tener al menos 2 caracteres.',
            'max_length' => 'El modelo no puede exceder 70 caracteres.'
        ],
        'sku' => [
            'max_length' => 'El SKU no puede exceder 50 caracteres.',
            'is_unique' => 'Este SKU ya existe en el sistema.'
        ],
        'numSerie' => [
            'max_length' => 'El número de serie no puede exceder 100 caracteres.',
            'is_unique' => 'Este número de serie ya existe en el sistema.'
        ],
        'cantDisponible' => [
            'required' => 'La cantidad disponible es obligatoria.',
            'integer' => 'La cantidad debe ser un número entero.',
            'greater_than_equal_to' => 'La cantidad no puede ser negativa.'
        ],
        'estado' => [
            'required' => 'El estado es obligatorio.',
            'in_list' => 'Debe seleccionar un estado válido.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['generarSKU'];
    protected $beforeUpdate = [];

    /**
     * Obtener todos los equipos con información de categoría y marca
     */
    public function getEquiposConDetalles()
    {
        return $this->select('equipo.*, cateEquipo.nomCate, marcaEquipo.nomMarca')
                    ->join('cateEquipo', 'equipo.idCateEquipo = cateEquipo.idCateEquipo')
                    ->join('marcaEquipo', 'equipo.idMarca = marcaEquipo.idMarca')
                    ->orderBy('equipo.idEquipo', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener un equipo específico con detalles
     */
    public function getEquipoConDetalles($id)
    {
        return $this->select('equipo.*, cateEquipo.nomCate, marcaEquipo.nomMarca')
                    ->join('cateEquipo', 'equipo.idCateEquipo = cateEquipo.idCateEquipo')
                    ->join('marcaEquipo', 'equipo.idMarca = marcaEquipo.idMarca')
                    ->where('equipo.idEquipo', $id)
                    ->first();
    }

    /**
     * Buscar equipos por criterios
     */
    public function buscarEquipos($criterios = [])
    {
        $builder = $this->select('equipo.*, cateEquipo.nomCate, marcaEquipo.nomMarca')
                        ->join('cateEquipo', 'equipo.idCateEquipo = cateEquipo.idCateEquipo')
                        ->join('marcaEquipo', 'equipo.idMarca = marcaEquipo.idMarca');

        if (!empty($criterios['categoria'])) {
            $builder->where('equipo.idCateEquipo', $criterios['categoria']);
        }

        if (!empty($criterios['marca'])) {
            $builder->where('equipo.idMarca', $criterios['marca']);
        }

        if (!empty($criterios['estado'])) {
            $builder->where('equipo.estado', $criterios['estado']);
        }

        if (!empty($criterios['modelo'])) {
            $builder->like('equipo.modelo', $criterios['modelo']);
        }

        return $builder->orderBy('equipo.idEquipo', 'DESC')->findAll();
    }

    /**
     * Obtener estadísticas del inventario
     */
    public function getEstadisticas()
    {
        $stats = [];
        
        // Total de equipos
        $stats['total'] = $this->countAll();
        
        // Equipos por estado
        $builder = $this->db->table($this->table);
        $builder->select('estado, COUNT(*) as cantidad');
        $builder->groupBy('estado');
        $stats['por_estado'] = $builder->get()->getResultArray();
        
        // Equipos por categoría
        $builder = $this->db->table($this->table);
        $builder->select('cateEquipo.nomCate, COUNT(*) as cantidad');
        $builder->join('cateEquipo', 'equipo.idCateEquipo = cateEquipo.idCateEquipo');
        $builder->groupBy('equipo.idCateEquipo');
        $stats['por_categoria'] = $builder->get()->getResultArray();
        
        // Cantidad total disponible
        $builder = $this->db->table($this->table);
        $builder->selectSum('cantDisponible', 'total_disponible');
        $result = $builder->get()->getRow();
        $stats['total_disponible'] = $result->total_disponible ?? 0;
        
        return $stats;
    }

    /**
     * Generar SKU automático si no se proporciona
     */
    protected function generarSKU(array $data)
    {
        if (empty($data['data']['sku'])) {
            // Generar SKU basado en categoría + marca + timestamp
            $categoria = $this->db->table('cateEquipo')
                                 ->select('nomCate')
                                 ->where('idCateEquipo', $data['data']['idCateEquipo'])
                                 ->get()->getRow();
            
            $marca = $this->db->table('marcaEquipo')
                             ->select('nomMarca')
                             ->where('idMarca', $data['data']['idMarca'])
                             ->get()->getRow();
            
            $catPrefix = $categoria ? strtoupper(substr($categoria->nomCate, 0, 3)) : 'EQP';
            $marcaPrefix = $marca ? strtoupper(substr($marca->nomMarca, 0, 3)) : 'GEN';
            $timestamp = date('ymdHis');
            $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
            
            $data['data']['sku'] = $catPrefix . '-' . $marcaPrefix . '-' . $timestamp . $random;
        }
        
        return $data;
    }
}