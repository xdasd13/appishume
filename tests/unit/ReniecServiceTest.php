<?php

namespace Tests\Unit;

use App\Libraries\ReniecService;
use App\Models\ReniecCacheModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Tests básicos para ReniecService
 * 
 * @author Sistema Ishume
 * @version 1.0
 */
class ReniecServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate     = true;
    protected $migrateOnce = false;
    protected $refresh     = true;
    protected $namespace   = null;

    protected $reniecService;
    protected $cacheModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reniecService = new ReniecService();
        $this->cacheModel = new ReniecCacheModel();
    }

    /**
     * Test validación de formato de DNI
     */
    public function testValidDniFormat()
    {
        // Test DNI válido
        $result = $this->reniecService->consultarDni('12345678');
        $this->assertArrayHasKey('status', $result);
        
        // Test DNI inválido - menos de 8 dígitos
        $result = $this->reniecService->consultarDni('1234567');
        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('8 dígitos', $result['message']);
        
        // Test DNI inválido - más de 8 dígitos
        $result = $this->reniecService->consultarDni('123456789');
        $this->assertEquals('error', $result['status']);
        
        // Test DNI inválido - contiene letras
        $result = $this->reniecService->consultarDni('1234567a');
        $this->assertEquals('error', $result['status']);
        
        // Test DNI vacío
        $result = $this->reniecService->consultarDni('');
        $this->assertEquals('error', $result['status']);
    }

    /**
     * Test cache de DNI
     */
    public function testDniCache()
    {
        // Insertar datos de prueba en cache
        $testData = [
            'nombres' => 'Juan Carlos',
            'apellido_paterno' => 'Pérez',
            'apellido_materno' => 'García',
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'M'
        ];
        
        $this->cacheModel->saveSuccessfulResponse('12345678', $testData);
        
        // Verificar que se encuentra en cache
        $cached = $this->cacheModel->findValidDni('12345678');
        $this->assertNotNull($cached);
        $this->assertEquals('Juan Carlos', $cached->nombres);
        $this->assertEquals('Pérez', $cached->apellido_paterno);
        
        // Consultar via servicio (debería usar cache)
        $result = $this->reniecService->consultarDni('12345678');
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('cache', $result['data']['source']);
        $this->assertEquals('Juan Carlos', $result['data']['nombres']);
    }

    /**
     * Test cache de errores
     */
    public function testErrorCache()
    {
        // Guardar error en cache
        $this->cacheModel->saveErrorResponse('87654321', 'DNI no encontrado en RENIEC');
        
        // Verificar que se encuentra en cache
        $cached = $this->cacheModel->findValidDni('87654321');
        $this->assertNotNull($cached);
        $this->assertFalse($cached->is_valid);
        $this->assertEquals('DNI no encontrado en RENIEC', $cached->error_message);
        
        // Consultar via servicio (debería usar cache de error)
        $result = $this->reniecService->consultarDni('87654321');
        $this->assertEquals('error', $result['status']);
        $this->assertStringContainsString('no encontrado', $result['message']);
    }

    /**
     * Test limpieza de cache expirado
     */
    public function testCacheExpiration()
    {
        // Insertar registro con fecha de expiración pasada
        $expiredData = [
            'dni' => '11111111',
            'nombres' => 'Test Expired',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Cache',
            'is_valid' => true,
            'consulted_at' => date('Y-m-d H:i:s', strtotime('-100 days')),
            'expires_at' => date('Y-m-d H:i:s', strtotime('-10 days')) // Expirado
        ];
        
        $this->cacheModel->insert($expiredData);
        
        // Verificar que no se encuentra en cache válido
        $cached = $this->cacheModel->findValidDni('11111111');
        $this->assertNull($cached);
        
        // Limpiar cache expirado
        $deletedCount = $this->cacheModel->cleanExpiredCache();
        $this->assertGreaterThan(0, $deletedCount);
    }

    /**
     * Test estadísticas del cache
     */
    public function testCacheStats()
    {
        // Insertar datos de prueba
        $this->cacheModel->saveSuccessfulResponse('22222222', [
            'nombres' => 'Test Stats',
            'apellido_paterno' => 'User',
            'apellido_materno' => 'Valid'
        ]);
        
        $this->cacheModel->saveErrorResponse('33333333', 'Test error');
        
        // Obtener estadísticas
        $stats = $this->cacheModel->getCacheStats();
        
        $this->assertArrayHasKey('total_records', $stats);
        $this->assertArrayHasKey('valid_cache', $stats);
        $this->assertArrayHasKey('successful_queries', $stats);
        $this->assertArrayHasKey('error_queries', $stats);
        $this->assertArrayHasKey('cache_hit_rate', $stats);
        
        $this->assertGreaterThan(0, $stats['total_records']);
        $this->assertGreaterThan(0, $stats['successful_queries']);
        $this->assertGreaterThan(0, $stats['error_queries']);
    }

    /**
     * Test modelo ReniecCache
     */
    public function testReniecCacheModel()
    {
        // Test validaciones del modelo
        $invalidData = [
            'dni' => '123', // DNI muy corto
            'is_valid' => true,
            'consulted_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->cacheModel->insert($invalidData);
        $this->assertFalse($result);
        
        // Test datos válidos
        $validData = [
            'dni' => '44444444',
            'nombres' => 'Test Valid',
            'apellido_paterno' => 'Model',
            'apellido_materno' => 'Insert',
            'is_valid' => true,
            'consulted_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->cacheModel->insert($validData);
        $this->assertIsNumeric($result);
        $this->assertGreaterThan(0, $result);
        
        // Verificar que se insertó correctamente
        $inserted = $this->cacheModel->find($result);
        $this->assertEquals('44444444', $inserted->dni);
        $this->assertEquals('Test Valid', $inserted->nombres);
        $this->assertTrue((bool)$inserted->is_valid);
    }

    /**
     * Test helper methods del modelo
     */
    public function testModelHelperMethods()
    {
        // Test isInValidCache
        $this->cacheModel->saveSuccessfulResponse('55555555', [
            'nombres' => 'Helper Test',
            'apellido_paterno' => 'User'
        ]);
        
        $this->assertTrue($this->cacheModel->isInValidCache('55555555'));
        $this->assertFalse($this->cacheModel->isInValidCache('99999999'));
    }

    /**
     * Test configuración del servicio
     */
    public function testServiceConfiguration()
    {
        // Verificar que el servicio se inicializa correctamente
        $this->assertInstanceOf(ReniecService::class, $this->reniecService);
        
        // Test obtener estadísticas
        $stats = $this->reniecService->getStats();
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_records', $stats);
    }

    /**
     * Test limpieza de cache via servicio
     */
    public function testServiceCacheCleanup()
    {
        // Insertar datos para limpiar
        $this->cacheModel->insert([
            'dni' => '66666666',
            'nombres' => 'To Delete',
            'is_valid' => true,
            'consulted_at' => date('Y-m-d H:i:s', strtotime('-200 days')),
            'expires_at' => date('Y-m-d H:i:s', strtotime('-100 days'))
        ]);
        
        // Limpiar via servicio
        $deletedCount = $this->reniecService->cleanExpiredCache();
        $this->assertIsInt($deletedCount);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
