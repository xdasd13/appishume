<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de categorías de equipos
 * Tabla: cateEquipo
 */
class CateEquipoModel extends Model
{
    protected $table = 'cateEquipo';
    protected $primaryKey = 'idCateEquipo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nomCate',
        'descripcion'
    ];

    // Validaciones
    protected $validationRules = [
        'nomCate' => 'required|string|min_length[2]|max_length[70]|is_unique[cateEquipo.nomCate,idCateEquipo,{idCateEquipo}]',
        'descripcion' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'nomCate' => [
            'required' => 'El nombre de la categoría es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 2 caracteres.',
            'max_length' => 'El nombre no puede exceder 70 caracteres.',
            'is_unique' => 'Esta categoría ya existe en el sistema.'
        ]
    ];

    /**
     * Obtener todas las categorías ordenadas por nombre
     */
    public function getCategorias()
    {
        return $this->orderBy('nomCate', 'ASC')->findAll();
    }

    /**
     * Obtener categorías con conteo de equipos
     */
    public function getCategoriasConConteo()
    {
        return $this->select('cateEquipo.*, COUNT(equipo.idEquipo) as total_equipos')
                    ->join('equipo', 'cateEquipo.idCateEquipo = equipo.idCateEquipo', 'left')
                    ->groupBy('cateEquipo.idCateEquipo')
                    ->orderBy('cateEquipo.nomCate', 'ASC')
                    ->findAll();
    }
}
