<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la gestión de ubicaciones físicas
 * Tabla: ubicacion
 */
class UbicacionModel extends Model
{
    protected $table = 'ubicacion';
    protected $primaryKey = 'idUbicacion';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'nombreUbicacion',
        'descripcion'
    ];

    // Validaciones
    protected $validationRules = [
        'nombreUbicacion' => 'required|string|min_length[2]|max_length[100]|is_unique[ubicacion.nombreUbicacion,idUbicacion,{idUbicacion}]',
        'descripcion' => 'permit_empty|string'
    ];

    protected $validationMessages = [
        'nombreUbicacion' => [
            'required' => 'El nombre de la ubicación es obligatorio.',
            'min_length' => 'El nombre debe tener al menos 2 caracteres.',
            'max_length' => 'El nombre no puede exceder 100 caracteres.',
            'is_unique' => 'Esta ubicación ya existe en el sistema.'
        ]
    ];

    /**
     * Obtener todas las ubicaciones ordenadas por nombre
     */
    public function getUbicaciones()
    {
        return $this->orderBy('nombreUbicacion', 'ASC')->findAll();
    }

    /**
     * Obtener ubicaciones con conteo de equipos asignados
     */
    public function getUbicacionesConConteo()
    {
        return $this->select('ubicacion.*, COUNT(equipoUbicacion.idEquipo) as total_equipos')
                    ->join('equipoUbicacion', 'ubicacion.idUbicacion = equipoUbicacion.idUbicacion', 'left')
                    ->groupBy('ubicacion.idUbicacion')
                    ->orderBy('ubicacion.nombreUbicacion', 'ASC')
                    ->findAll();
    }
}
