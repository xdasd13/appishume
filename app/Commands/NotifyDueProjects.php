<?php

namespace App\Commands;

use App\Services\DueReminderService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use InvalidArgumentException;

class NotifyDueProjects extends BaseCommand
{
    protected $group = 'notifications';

    protected $name = 'notifications:due-reminders';

    protected $description = 'Genera notificaciones para proyectos que vencen en 3 días.';

    protected $usage = 'notifications:due-reminders [--date YYYY-MM-DD]';

    protected $options = [
        '--date' => 'Fecha objetivo en formato YYYY-MM-DD (por defecto: hoy + 3 días)',
    ];

    public function run(array $params)
    {
        $dateOption = CLI::getOption('date');

        try {
            $service = new DueReminderService();
            $summary = $service->run($dateOption ?: null);

            CLI::write('=== Recordatorio de vencimientos ===', 'yellow');
            CLI::write('Fecha objetivo: ' . $summary['date'], 'white');
            CLI::write('Servicios evaluados: ' . $summary['total'], 'white');
            CLI::write('Notificaciones creadas: ' . $summary['created'], 'green');
            CLI::write('Notificaciones saltadas: ' . $summary['skipped'], 'cyan');
            CLI::write('Errores: ' . $summary['errors'], $summary['errors'] > 0 ? 'red' : 'green');

            if ($summary['errors'] > 0) {
                CLI::write('Revisar el log para más detalles.', 'red');
            }
        } catch (InvalidArgumentException $e) {
            CLI::error('Fecha inválida: ' . $e->getMessage());
        } catch (\Throwable $th) {
            CLI::error('Error al ejecutar el recordatorio: ' . $th->getMessage());
            CLI::write($th->getTraceAsString(), 'red');
        }
    }
}
