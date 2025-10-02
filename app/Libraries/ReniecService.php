<?php

namespace App\Libraries;

use App\Models\ReniecCacheModel;
use CodeIgniter\HTTP\CURLRequest;
use Exception;

/**
 * Servicio para consultas RENIEC via API Decolecta
 * 
 * Funcionalidades:
 * - Consulta DNI con cache automático (90 días TTL)
 * - Rate limiting y manejo de errores
 * - Logging de todas las operaciones
 * - Validaciones de seguridad
 * 
 * @author Sistema Ishume - Chincha Alta
 * @version 1.0
 */
class ReniecService
{
    private $client;
    private $cacheModel;
    private $apiUrl;
    private $apiToken;
    private $timeout;
    private $maxRetries;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest();
        $this->cacheModel = new ReniecCacheModel();
        
        // Load environment variables from envxD file
        $this->loadEnvironmentVariables();
        
        // Configuración desde variables de entorno
        $this->apiUrl = $this->getEnvVar('DECOLECTA_API_URL', 'https://api.decolecta.com/v1/reniec/dni');
        $this->apiToken = $this->getEnvVar('DECOLECTA_API_TOKEN', 'sk_10069.nuBfTnQrhikrkOdGQ44JvDUJZvJx3NEk');
        $this->timeout = (int)$this->getEnvVar('DECOLECTA_TIMEOUT', '10');
        $this->maxRetries = (int)$this->getEnvVar('DECOLECTA_MAX_RETRIES', '2');

        // Validar configuración
        if (empty($this->apiToken)) {
            log_message('error', 'ReniecService: Token de Decolecta no configurado');
        } else {
            log_message('info', 'ReniecService: Token configurado correctamente');
        }
    }

    /**
     * Consultar DNI con cache automático
     * 
     * @param string $dni DNI de 8 dígitos
     * @return array Resultado con status, data y message
     */
    public function consultarDni(string $dni): array
    {
        try {
            // Validar formato de DNI
            if (!$this->isValidDniFormat($dni)) {
                return $this->errorResponse('DNI debe tener exactamente 8 dígitos numéricos');
            }

            // Buscar en cache primero
            $cachedData = $this->cacheModel->findValidDni($dni);
            if ($cachedData) {
                log_message('info', "ReniecService: Cache HIT para DNI {$dni}");
                
                if ($cachedData->is_valid) {
                    return $this->successResponse([
                        'dni' => $cachedData->dni,
                        'nombres' => $cachedData->nombres,
                        'apellido_paterno' => $cachedData->apellido_paterno,
                        'apellido_materno' => $cachedData->apellido_materno,
                        'apellidos_completos' => trim($cachedData->apellido_paterno . ' ' . $cachedData->apellido_materno),
                        'fecha_nacimiento' => $cachedData->fecha_nacimiento,
                        'sexo' => $cachedData->sexo,
                        'estado_civil' => $cachedData->estado_civil,
                        'ubigeo' => $cachedData->ubigeo,
                        'direccion' => $cachedData->direccion,
                        'source' => 'cache'
                    ]);
                } else {
                    return $this->errorResponse($cachedData->error_message ?: 'DNI no encontrado en RENIEC');
                }
            }

            // Cache MISS - consultar API
            log_message('info', "ReniecService: Cache MISS para DNI {$dni} - consultando API");
            return $this->consultarApiDecolecta($dni);

        } catch (Exception $e) {
            log_message('error', "ReniecService: Error general consultando DNI {$dni}: " . $e->getMessage());
            return $this->errorResponse('Error interno del servicio');
        }
    }

    /**
     * Consultar API de Decolecta con reintentos
     * 
     * @param string $dni DNI a consultar
     * @return array Resultado de la consulta
     */
    private function consultarApiDecolecta(string $dni): array
    {
        $attempts = 0;
        $lastError = '';

        while ($attempts < $this->maxRetries) {
            $attempts++;
            // Construir URL con parámetros
            $url = $this->apiUrl . '?numero=' . $dni;
            
            // Log de la consulta
            log_message('info', "ReniecService: Consultando DNI {$dni} - Intento {$attempts}");
            log_message('debug', "ReniecService: URL completa = {$url}");
            log_message('debug', "ReniecService: Token = " . substr($this->apiToken, 0, 20) . "...");

            // Realizar consulta HTTP
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $this->apiToken,
                    'Accept: application/json',
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Log de la respuesta
            log_message('info', "ReniecService: API respondió con status {$statusCode} para DNI {$dni}");
            
            if ($curlError) {
                log_message('error', "ReniecService: cURL Error para DNI {$dni}: {$curlError}");
                $lastError = "Error de conexión: {$curlError}";
                continue;
            }

            log_message('debug', "ReniecService: Respuesta cruda: " . substr($response, 0, 500));
            
            $data = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                log_message('error', "ReniecService: Error JSON para DNI {$dni}: " . json_last_error_msg());
                $lastError = "Error decodificando respuesta JSON";
                continue;
            }

            try {
                // Procesar respuesta según status code
                switch ($statusCode) {
                    case 200:
                        return $this->procesarRespuestaExitosa($dni, $data);
                    
                    case 404:
                        return $this->procesarDniNoEncontrado($dni, $data);
                    
                    case 401:
                        $error = 'Token de API inválido o expirado';
                        log_message('error', "ReniecService: {$error}");
                        return $this->errorResponse($error);
                    
                    case 429:
                        $error = 'Límite de consultas excedido - intente más tarde';
                        log_message('warning', "ReniecService: Rate limit excedido para DNI {$dni}");
                        
                        if ($attempts < $this->maxRetries) {
                            sleep(2 ** $attempts); // Backoff exponencial
                            continue 2; // Continuar el bucle while exterior
                        }
                        return $this->errorResponse($error);
                    
                    case 500:
                    case 502:
                    case 503:
                        $error = 'Servicio RENIEC temporalmente no disponible';
                        log_message('error', "ReniecService: Error del servidor ({$statusCode}) para DNI {$dni}");
                        
                        if ($attempts < $this->maxRetries) {
                            sleep(1);
                            continue 2; // Continuar el bucle while exterior
                        }
                        return $this->errorResponse($error);
                    
                    default:
                        $error = "Error inesperado del servicio (HTTP {$statusCode})";
                        log_message('error', "ReniecService: {$error} para DNI {$dni}");
                        return $this->errorResponse($error);
                }

            } catch (Exception $e) {
                $lastError = $e->getMessage();
                log_message('error', "ReniecService: Excepción en intento {$attempts} para DNI {$dni}: {$lastError}");
                
                if ($attempts < $this->maxRetries) {
                    sleep(1);
                    continue; // Este continue está bien porque está directamente en el while
                }
            }
        }

        // Todos los intentos fallaron
        $this->cacheModel->saveErrorResponse($dni, "Error de conexión: {$lastError}");
        return $this->errorResponse('No se pudo conectar con el servicio RENIEC');
    }

    /**
     * Procesar respuesta exitosa de la API
     * 
     * @param string $dni DNI consultado
     * @param array $apiData Datos de la API
     * @return array Respuesta procesada
     */
    private function procesarRespuestaExitosa(string $dni, array $apiData): array
    {
        try {
            // Validar que tenemos los campos mínimos necesarios
            if (!isset($apiData['document_number']) || $apiData['document_number'] !== $dni) {
                throw new Exception('DNI en respuesta no coincide con el solicitado');
            }

            // Mapear campos de Decolecta a nuestro formato
            $normalizedData = [
                'nombres' => $apiData['first_name'] ?? '',
                'apellido_paterno' => $apiData['first_last_name'] ?? '',
                'apellido_materno' => $apiData['second_last_name'] ?? '',
                'fecha_nacimiento' => null, // Decolecta no devuelve fecha de nacimiento
                'sexo' => null, // Decolecta no devuelve sexo
                'estado_civil' => null, // Decolecta no devuelve estado civil
                'ubigeo' => null, // Decolecta no devuelve ubigeo
                'direccion' => null, // Decolecta no devuelve dirección
                'full_name' => $apiData['full_name'] ?? ''
            ];

            // Guardar en cache
            $this->cacheModel->saveSuccessfulResponse($dni, $normalizedData);

            // Preparar respuesta
            $responseData = array_merge($normalizedData, [
                'dni' => $dni,
                'apellidos_completos' => trim($normalizedData['apellido_paterno'] . ' ' . $normalizedData['apellido_materno']),
                'source' => 'api'
            ]);

            log_message('info', "ReniecService: DNI {$dni} consultado exitosamente desde API");
            return $this->successResponse($responseData);

        } catch (Exception $e) {
            log_message('error', "ReniecService: Error procesando respuesta exitosa para DNI {$dni}: " . $e->getMessage());
            $this->cacheModel->saveErrorResponse($dni, 'Error procesando datos de RENIEC');
            return $this->errorResponse('Error procesando datos de RENIEC');
        }
    }

    /**
     * Procesar DNI no encontrado
     * 
     * @param string $dni DNI consultado
     * @param array $apiData Datos de la API
     * @return array Respuesta de error
     */
    private function procesarDniNoEncontrado(string $dni, array $apiData): array
    {
        $errorMessage = $apiData['message'] ?? 'DNI no encontrado en RENIEC';
        
        // Guardar en cache como no válido
        $this->cacheModel->saveErrorResponse($dni, $errorMessage, $apiData);
        
        log_message('info', "ReniecService: DNI {$dni} no encontrado en RENIEC");
        return $this->errorResponse($errorMessage);
    }

    /**
     * Validar formato de DNI
     * 
     * @param string $dni DNI a validar
     * @return bool True si es válido
     */
    private function isValidDniFormat(string $dni): bool
    {
        return preg_match('/^\d{8}$/', $dni) === 1;
    }

    /**
     * Respuesta de éxito estandarizada
     * 
     * @param array $data Datos de respuesta
     * @return array Respuesta formateada
     */
    private function successResponse(array $data): array
    {
        return [
            'status' => 'success',
            'data' => $data,
            'message' => 'DNI consultado exitosamente'
        ];
    }

    /**
     * Respuesta de error estandarizada
     * 
     * @param string $message Mensaje de error
     * @return array Respuesta formateada
     */
    private function errorResponse(string $message): array
    {
        return [
            'status' => 'error',
            'data' => null,
            'message' => $message
        ];
    }

    /**
     * Obtener estadísticas del servicio
     * 
     * @return array Estadísticas
     */
    public function getStats(): array
    {
        return $this->cacheModel->getCacheStats();
    }

    /**
     * Limpiar cache expirado
     * 
     * @return int Registros eliminados
     */
    public function cleanExpiredCache(): int
    {
        return $this->cacheModel->cleanExpiredCache();
    }

    /**
     * Load environment variables from envxD file
     */
    private function loadEnvironmentVariables(): void
    {
        $envFile = FCPATH . '../envxD';
        
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                
                // Parse key=value pairs
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    
                    // Set environment variable if not already set
                    if (!getenv($key)) {
                        putenv("$key=$value");
                    }
                }
            }
        }
    }

    /**
     * Get environment variable with fallback
     * 
     * @param string $key Environment variable key
     * @param string $default Default value if not found
     * @return string Environment variable value or default
     */
    private function getEnvVar(string $key, string $default = ''): string
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}
