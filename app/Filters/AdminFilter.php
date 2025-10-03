<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter - Middleware para Control de Acceso de Administradores
 * 
 * Este filtro implementa el patrón Middleware para interceptar solicitudes
 * y verificar que el usuario tenga permisos de administrador antes de
 * permitir el acceso a rutas sensibles.
 * 
 * Funcionalidades:
 * - Verificación de autenticación (sesión activa)
 * - Verificación de rol de administrador
 * - Logging de intentos de acceso no autorizados
 * - Redirección segura con mensajes informativos
 */
class AdminFilter implements FilterInterface
{
    /**
     * Método before() - Se ejecuta ANTES del controlador
     * 
     * Este es el corazón del middleware. Intercepta la solicitud
     * y verifica permisos antes de que llegue al controlador.
     * 
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Verificar si existe una sesión activa
        if (!session()->get('usuario_logueado')) {
            // Log del intento de acceso no autorizado
            log_message('warning', 'Intento de acceso sin autenticación a ruta administrativa: ' . $request->getUri());
            
            return redirect()->to('/login')
                ->with('error', 'Debes iniciar sesión para acceder a esta sección.');
        }

        // 2. Verificar el rol específico de administrador
        $rolUsuario = session()->get('role') ?? session()->get('tipo_usuario');
        
        if ($rolUsuario !== 'administrador' && $rolUsuario !== 'admin') {
            // Log del intento de acceso con permisos insuficientes
            $usuarioId = session()->get('usuario_id');
            $usuarioNombre = session()->get('usuario_nombre') ?? 'Desconocido';
            
            log_message('warning', sprintf(
                'Usuario %s (ID: %s, Rol: %s) intentó acceder a ruta administrativa: %s',
                $usuarioNombre,
                $usuarioId,
                $rolUsuario,
                $request->getUri()
            ));

            // Redirección con mensaje específico según el rol
            $mensaje = $this->getMensajeSegunRol($rolUsuario);
            
            return redirect()->to('/dashboard')
                ->with('error', $mensaje);
        }

        // 3. Log de acceso exitoso (opcional, para auditoría)
        if (ENVIRONMENT === 'development') {
            $usuarioNombre = session()->get('usuario_nombre') ?? 'Admin';
            log_message('info', "Acceso administrativo autorizado para: {$usuarioNombre} a " . $request->getUri());
        }

        // 4. Permitir que continúe la ejecución
        return null;
    }

    /**
     * Método after() - Se ejecuta DESPUÉS del controlador
     * 
     * Se puede usar para modificar la respuesta antes de enviarla al cliente.
     * En este caso, agregamos headers de seguridad adicionales.
     * 
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Agregar headers de seguridad para rutas administrativas
        $response->setHeader('X-Admin-Access', 'true');
        $response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->setHeader('Pragma', 'no-cache');
        $response->setHeader('Expires', '0');
    }

    /**
     * Genera mensaje personalizado según el rol del usuario
     * 
     * @param string|null $rol
     * @return string
     */
    private function getMensajeSegunRol(?string $rol): string
    {
        switch ($rol) {
            case 'trabajador':
                return 'Esta vista solo está disponible para administradores.';
            case 'supervisor':
                return 'Esta vista solo está disponible para administradores.';
            case 'financiero':
                return 'Esta vista solo está disponible para administradores.';
            default:
                return 'Esta vista solo está disponible para administradores.';
        }
    }
}
