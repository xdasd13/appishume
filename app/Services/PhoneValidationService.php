<?php

namespace App\Services;

class PhoneValidationService
{
    private $apiKey;
    private $baseUrl = 'https://api.numlookupapi.com/v1/validate';

    public function __construct()
    {
        // Configurar la API key desde las variables de entorno o configuración
        $this->apiKey = getenv('NUMLOOKUP_API_KEY') ?: 'num_live_gm5kXMYblwfVUnIolB49MUFzJ26Pr6aekWZ44z2f';
    }

    /**
     * Validar un número de teléfono usando NumLookup API
     * 
     * @param string $phoneNumber Número de teléfono a validar
     * @param string|null $countryCode Código de país opcional (ej: 'US', 'PE')
     * @return array Resultado de la validación
     */
    public function validatePhoneNumber($phoneNumber, $countryCode = null)
    {
        try {
            // Limpiar y formatear el número
            $cleanNumber = $this->cleanPhoneNumber($phoneNumber);
            
            // Log para debug
            log_message('info', 'Número original: ' . $phoneNumber);
            log_message('info', 'Número limpio: ' . $cleanNumber);
            
            // Construir la URL de la API
            $url = $this->buildApiUrl($cleanNumber, $countryCode);
            
            // Log de la URL
            log_message('info', 'URL de la API: ' . $url);
            
            // Realizar la petición HTTP
            $response = $this->makeHttpRequest($url);
            
            // Procesar la respuesta
            return $this->processResponse($response, $cleanNumber);
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'valid' => false,
                'error' => $e->getMessage(),
                'number' => $phoneNumber
            ];
        }
    }

    /**
     * Limpiar y formatear el número de teléfono
     */
    private function cleanPhoneNumber($phoneNumber)
    {
        // Solo limpiar espacios y caracteres especiales, mantener el +51
        $cleaned = trim($phoneNumber);
        
        // Log para debug
        log_message('info', 'Número recibido para limpiar: ' . $phoneNumber);
        log_message('info', 'Número después de trim: ' . $cleaned);
        
        return $cleaned;
    }

    /**
     * Construir la URL de la API
     */
    private function buildApiUrl($phoneNumber, $countryCode = null)
    {
        // Remover el +51 del número para enviarlo a la API
        $numberForApi = $phoneNumber;
        if (strpos($numberForApi, '+51') === 0) {
            $numberForApi = substr($numberForApi, 3); // Remover +51
        }
        
        // Log para debug
        log_message('info', 'Número para API: ' . $numberForApi);
        
        $url = $this->baseUrl . '/' . urlencode($numberForApi);
        
        $params = ['apikey' => $this->apiKey];
        
        if ($countryCode) {
            $params['country_code'] = $countryCode;
        }
        
        $finalUrl = $url . '?' . http_build_query($params);
        
        // Log de la URL final
        log_message('info', 'URL final: ' . $finalUrl);
        
        return $finalUrl;
    }

    /**
     * Realizar la petición HTTP usando cURL
     */
    private function makeHttpRequest($url)
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: AppiShume/1.0'
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("Error cURL: " . $error);
        }
        
        if ($httpCode !== 200) {
            throw new \Exception("Error HTTP: " . $httpCode);
        }
        
        return $response;
    }

    /**
     * Procesar la respuesta de la API
     */
    private function processResponse($response, $originalNumber)
    {
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Error al decodificar JSON: " . json_last_error_msg());
        }
        
        // Log para debug
        log_message('info', 'API Response decoded: ' . json_encode($data));
        log_message('info', 'Valid field: ' . ($data['valid'] ?? 'not set'));
        log_message('info', 'Valid type: ' . gettype($data['valid'] ?? 'null'));
        
        return [
            'success' => true,
            'valid' => $data['valid'] ?? false,
            'number' => $data['number'] ?? $originalNumber,
            'local_format' => $data['local_format'] ?? null,
            'international_format' => $data['international_format'] ?? null,
            'country_prefix' => $data['country_prefix'] ?? null,
            'country_code' => $data['country_code'] ?? null,
            'country_name' => $data['country_name'] ?? null,
            'location' => $data['location'] ?? null,
            'carrier' => $data['carrier'] ?? null,
            'line_type' => $data['line_type'] ?? null,
            'raw_response' => $data
        ];
    }

    /**
     * Validar múltiples números de teléfono
     */
    public function validateMultipleNumbers($phoneNumbers, $countryCode = null)
    {
        $results = [];
        
        foreach ($phoneNumbers as $number) {
            $results[$number] = $this->validatePhoneNumber($number, $countryCode);
        }
        
        return $results;
    }

    /**
     * Verificar si un número es válido (método de conveniencia)
     */
    public function isValid($phoneNumber, $countryCode = null)
    {
        $result = $this->validatePhoneNumber($phoneNumber, $countryCode);
        return $result['success'] && $result['valid'];
    }

    /**
     * Obtener información detallada de un número válido
     */
    public function getPhoneInfo($phoneNumber, $countryCode = null)
    {
        $result = $this->validatePhoneNumber($phoneNumber, $countryCode);
        
        if ($result['success'] && $result['valid']) {
            return [
                'formatted_number' => $result['international_format'],
                'country' => $result['country_name'],
                'carrier' => $result['carrier'],
                'line_type' => $result['line_type'],
                'location' => $result['location']
            ];
        }
        
        return null;
    }

    /**
     * Configurar API key
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Obtener API key actual
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
