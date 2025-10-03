<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * TrabajadorFilter - Middleware para Control de Acceso de Trabajadores
 * 
 * Este filtro permite el acceso tanto a trabajadores como a administradores,
 * pero bloquea usuarios no autenticados o con roles no válidos.
 */
class TrabajadorFilter implements FilterInterface
{
    /**
     * Roles permitidos para acceder a rutas de trabajador
     */
    private array $rolesPermitidos = [
        'trabajador',
        'administrador',
        'admin',
        'supervisor'
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar autenticación
        if (!session()->get('usuario_logueado')) {
            log_message('warning', 'Intento de acceso sin autenticación a ruta de trabajador: ' . $request->getUri());
            
            return redirect()->to('/login')
                ->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Verificar rol permitido
        $rolUsuario = session()->get('role') ?? session()->get('tipo_usuario');
        
        if (!in_array($rolUsuario, $this->rolesPermitidos)) {
            log_message('warning', sprintf(
                'Usuario con rol %s intentó acceder a ruta de trabajador: %s',
                $rolUsuario,
                $request->getUri()
            ));

            return redirect()->to('/dashboard')
                ->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Headers específicos para rutas de trabajador
        $response->setHeader('X-Worker-Access', 'true');
    }
}
