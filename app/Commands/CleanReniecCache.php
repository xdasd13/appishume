<?php

namespace App\Commands;

use App\Libraries\ReniecService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Comando para limpiar cache expirado de RENIEC
 * 
 * Uso: php spark reniec:clean-cache
 * 
 * @author Sistema Ishume
 * @version 1.0
 */
class CleanReniecCache extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'RENIEC';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'reniec:clean-cache';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Limpia el cache expirado de consultas RENIEC (registros con más de 90 días)';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'reniec:clean-cache [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--dry-run' => 'Mostrar qué registros se eliminarían sin eliminarlos realmente',
        '--force'   => 'Forzar limpieza sin confirmación',
        '--stats'   => 'Mostrar estadísticas detalladas del cache'
    ];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        CLI::write('=== Limpieza de Cache RENIEC ===', 'yellow');
        CLI::newLine();

        try {
            $reniecService = new ReniecService();
            
            // Mostrar estadísticas si se solicita
            if (CLI::getOption('stats')) {
                $this->showStats($reniecService);
                CLI::newLine();
            }

            // Dry run - solo mostrar qué se eliminaría
            if (CLI::getOption('dry-run')) {
                $this->dryRun($reniecService);
                return;
            }

            // Confirmación si no se usa --force
            if (!CLI::getOption('force')) {
                $confirm = CLI::prompt('¿Está seguro de que desea limpiar el cache expirado?', ['y', 'n']);
                if ($confirm !== 'y') {
                    CLI::write('Operación cancelada.', 'yellow');
                    return;
                }
            }

            // Ejecutar limpieza
            CLI::write('Limpiando cache expirado...', 'blue');
            $deletedCount = $reniecService->cleanExpiredCache();

            if ($deletedCount > 0) {
                CLI::write("✓ Se eliminaron {$deletedCount} registros expirados.", 'green');
            } else {
                CLI::write('✓ No se encontraron registros expirados para eliminar.', 'green');
            }

            // Mostrar estadísticas finales
            CLI::newLine();
            CLI::write('Estadísticas actuales:', 'yellow');
            $this->showStats($reniecService);

        } catch (\Exception $e) {
            CLI::error('Error durante la limpieza: ' . $e->getMessage());
            CLI::write('Stack trace:', 'red');
            CLI::write($e->getTraceAsString(), 'red');
        }
    }

    /**
     * Mostrar estadísticas del cache
     */
    private function showStats(ReniecService $reniecService): void
    {
        $stats = $reniecService->getStats();
        
        CLI::write('📊 Estadísticas del Cache RENIEC:', 'cyan');
        CLI::write('  • Total de registros: ' . number_format($stats['total_records']), 'white');
        CLI::write('  • Cache válido: ' . number_format($stats['valid_cache']), 'green');
        CLI::write('  • Cache expirado: ' . number_format($stats['expired_cache']), 'red');
        CLI::write('  • Consultas exitosas: ' . number_format($stats['successful_queries']), 'green');
        CLI::write('  • Consultas con error: ' . number_format($stats['error_queries']), 'red');
        CLI::write('  • Tasa de acierto: ' . $stats['cache_hit_rate'] . '%', 'yellow');
    }

    /**
     * Dry run - mostrar qué se eliminaría
     */
    private function dryRun(ReniecService $reniecService): void
    {
        CLI::write('🔍 Modo DRY RUN - Solo mostrando qué se eliminaría:', 'yellow');
        CLI::newLine();

        // Obtener estadísticas actuales
        $stats = $reniecService->getStats();
        
        if ($stats['expired_cache'] > 0) {
            CLI::write("Se eliminarían {$stats['expired_cache']} registros expirados.", 'red');
            CLI::write('Esto liberaría espacio en la base de datos.', 'blue');
        } else {
            CLI::write('No hay registros expirados para eliminar.', 'green');
        }

        CLI::newLine();
        CLI::write('Para ejecutar la limpieza real, use:', 'yellow');
        CLI::write('  php spark reniec:clean-cache --force', 'white');
    }
}
