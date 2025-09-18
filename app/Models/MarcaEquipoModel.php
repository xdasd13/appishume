<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de marcas de equipos
 * Tabla: marcaEquipo
 */
class MarcaEquipoModel extends Model
{
    protected $table = 'marcaEquipo';
    protected $primaryKey = 'idMarca';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomMarca'
    ];

    // Validaciones
    protected $validationRules = [
        'nomMarca' => 'required|string|min_length[2]|max_length[70]|is_unique[marcaEquipo.nomMarca,idMarca,{idMarca}]'
    ];

    protected $validationMessages = [
        'nomMarca' => [
            'required' => 'El nombre de la marca es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 2 caracteres.',
            'max_length' => 'El nombre no puede exceder 70 caracteres.',
            'is_unique' => 'Esta marca ya existe en el sistema.'
        ]
    ];

    /**
     * Obtener todas las marcas ordenadas por nombre
     */
    public function getMarcas()
    {
        return $this->orderBy('nomMarca', 'ASC')->findAll();
    }

    /**
     * Obtener marcas con conteo de equipos
     */
    public function getMarcasConConteo()
    {
        return $this->select('marcaEquipo.*, COUNT(equipo.idEquipo) as total_equipos')
                    ->join('equipo', 'marcaEquipo.idMarca = equipo.idMarca', 'left')
                    ->groupBy('marcaEquipo.idMarca')
                    ->orderBy('marcaEquipo.nomMarca', 'ASC')
                    ->findAll();
    }
}
