<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para gestionar el cache de consultas RENIEC
 * 
 * @author Sistema Ishume
 * @version 1.0
 */
class ReniecCacheModel extends Model
{
    protected $table            = 'reniec_cache';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'dni',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'ubigeo',
        'direccion',
        'api_response',
        'is_valid',
        'error_message',
        'consulted_at',
        'expires_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'dni' => [
            'rules' => 'required|exact_length[8]|numeric|is_unique[reniec_cache.dni,id,{id}]',
            'errors' => [
                'required' => 'El DNI es obligatorio',
                'exact_length' => 'El DNI debe tener exactamente 8 dígitos',
                'numeric' => 'El DNI debe ser numérico',
                'is_unique' => 'Este DNI ya está en cache'
            ]
        ],
        'is_valid' => 'required|in_list[0,1]',
        'consulted_at' => 'required|valid_date',
        'expires_at' => 'required|valid_date'
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setExpirationDate'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Buscar DNI en cache válido (no expirado)
     * 
     * @param string $dni DNI de 8 dígitos
     * @return object|null Datos del DNI o null si no existe/expiró
     */
    public function findValidDni(string $dni): ?object
    {
        return $this->where('dni', $dni)
                   ->where('expires_at >', date('Y-m-d H:i:s'))
                   ->first();
    }

    /**
     * Verificar si un DNI está en cache y es válido
     * 
     * @param string $dni DNI de 8 dígitos
     * @return bool True si está en cache válido
     */
    public function isInValidCache(string $dni): bool
    {
        return $this->findValidDni($dni) !== null;
    }

    /**
     * Guardar respuesta exitosa de RENIEC
     * 
     * @param string $dni DNI consultado
     * @param array $reniecData Datos desde la API
     * @return bool|int ID del registro o false si falla
     */
    public function saveSuccessfulResponse(string $dni, array $reniecData)
    {
        $data = [
            'dni' => $dni,
            'nombres' => $reniecData['nombres'] ?? null,
            'apellido_paterno' => $reniecData['apellido_paterno'] ?? null,
            'apellido_materno' => $reniecData['apellido_materno'] ?? null,
            'fecha_nacimiento' => $reniecData['fecha_nacimiento'] ?? null,
            'sexo' => $reniecData['sexo'] ?? null,
            'estado_civil' => $reniecData['estado_civil'] ?? null,
            'ubigeo' => $reniecData['ubigeo'] ?? null,
            'direccion' => $reniecData['direccion'] ?? null,
            'api_response' => json_encode($reniecData),
            'is_valid' => true,
            'error_message' => null,
            'consulted_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Guardar respuesta de error de RENIEC
     * 
     * @param string $dni DNI consultado
     * @param string $errorMessage Mensaje de error
     * @param array|null $apiResponse Respuesta completa de la API
     * @return bool|int ID del registro o false si falla
     */
    public function saveErrorResponse(string $dni, string $errorMessage, ?array $apiResponse = null)
    {
        $data = [
            'dni' => $dni,
            'nombres' => null,
            'apellido_paterno' => null,
            'apellido_materno' => null,
            'fecha_nacimiento' => null,
            'sexo' => null,
            'estado_civil' => null,
            'ubigeo' => null,
            'direccion' => null,
            'api_response' => $apiResponse ? json_encode($apiResponse) : null,
            'is_valid' => false,
            'error_message' => $errorMessage,
            'consulted_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    /**
     * Limpiar cache expirado (para ejecutar en cron)
     * 
     * @return int Número de registros eliminados
     */
    public function cleanExpiredCache(): int
    {
        $deletedCount = $this->where('expires_at <', date('Y-m-d H:i:s'))->countAllResults();
        $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
        
        return $deletedCount;
    }

    /**
     * Obtener estadísticas del cache
     * 
     * @return array Estadísticas del cache
     */
    public function getCacheStats(): array
    {
        $total = $this->countAll();
        $valid = $this->where('expires_at >', date('Y-m-d H:i:s'))->countAllResults();
        $expired = $total - $valid;
        $successful = $this->where('is_valid', true)->countAllResults();
        $errors = $this->where('is_valid', false)->countAllResults();

        return [
            'total_records' => $total,
            'valid_cache' => $valid,
            'expired_cache' => $expired,
            'successful_queries' => $successful,
            'error_queries' => $errors,
            'cache_hit_rate' => $total > 0 ? round(($valid / $total) * 100, 2) : 0
        ];
    }

    /**
     * Callback: Establecer fecha de expiración (90 días)
     * 
     * @param array $data Datos a insertar
     * @return array Datos modificados
     */
    protected function setExpirationDate(array $data): array
    {
        if (!isset($data['data']['expires_at'])) {
            $data['data']['expires_at'] = date('Y-m-d H:i:s', strtotime('+90 days'));
        }
        
        return $data;
    }
}
