<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter - Middleware Base de Autenticación
 * 
 * Este filtro maneja la autenticación básica y puede aceptar argumentos
 * para verificar roles específicos. Es el filtro más flexible del sistema.
 * 
 * Uso:
 * - ['filter' => 'auth'] - Solo verificar autenticación
 * - ['filter' => 'auth:admin'] - Verificar autenticación + rol admin
 * - ['filter' => 'auth:trabajador'] - Verificar autenticación + rol trabajador
 */
class AuthFilter implements FilterInterface
{
    /**
     * Mapeo de roles para compatibilidad
     */
    private array $roleMapping = [
        'admin' => ['administrador', 'admin'],
        'trabajador' => ['trabajador', 'supervisor'],
        'administrador' => ['administrador', 'admin'],
        'supervisor' => ['supervisor', 'administrador', 'admin'],
        'financiero' => ['financiero', 'administrador', 'admin']
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Verificación básica de autenticación
        if (!session()->get('usuario_logueado')) {
            $this->logUnauthorizedAccess($request, 'No autenticado');
            
            return redirect()->to('/login')
                ->with('error', 'Debes iniciar sesión para acceder.');
        }

        // 2. Verificación de rol específico si se proporciona
        if (!empty($arguments)) {
            $rolRequerido = $arguments[0];
            $rolUsuario = session()->get('role') ?? session()->get('tipo_usuario');
            
            if (!$this->verificarRol($rolUsuario, $rolRequerido)) {
                $this->logUnauthorizedAccess($request, "Rol insuficiente: {$rolUsuario} vs {$rolRequerido}");
                
                $mensaje = $this->getMensajeAccesoDenegado($rolRequerido, $rolUsuario);
                
                return redirect()->to($this->getRedirectUrl($rolUsuario))
                    ->with('error', $mensaje);
            }
        }

        // 3. Verificar si la sesión no ha expirado (opcional)
        $this->verificarExpiracionSesion();

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Agregar headers de seguridad básicos
        $response->setHeader('X-Authenticated', 'true');
        
        // Actualizar último acceso en sesión
        session()->set('ultimo_acceso', time());
    }

    /**
     * Verifica si el rol del usuario es compatible con el rol requerido
     */
    private function verificarRol(?string $rolUsuario, string $rolRequerido): bool
    {
        if (!$rolUsuario) {
            return false;
        }

        // Si el rol requerido existe en el mapeo, usar la lista de roles compatibles
        if (isset($this->roleMapping[$rolRequerido])) {
            return in_array($rolUsuario, $this->roleMapping[$rolRequerido]);
        }

        // Comparación directa si no hay mapeo
        return $rolUsuario === $rolRequerido;
    }

    /**
     * Genera mensaje personalizado según el contexto
     */
    private function getMensajeAccesoDenegado(string $rolRequerido, ?string $rolUsuario): string
    {
        $mensajes = [
            'admin' => 'Esta sección requiere permisos de administrador.',
            'trabajador' => 'Esta sección es para personal autorizado.',
            'supervisor' => 'Esta sección requiere permisos de supervisión.',
            'financiero' => 'Esta sección requiere permisos financieros.'
        ];

        return $mensajes[$rolRequerido] ?? 'No tienes permisos para acceder a esta sección.';
    }

    /**
     * Determina la URL de redirección según el rol del usuario
     */
    private function getRedirectUrl(?string $rolUsuario): string
    {
        // Todos los usuarios van al dashboard principal
        return '/dashboard';
    }

    /**
     * Registra intentos de acceso no autorizados
     */
    private function logUnauthorizedAccess(RequestInterface $request, string $razon): void
    {
        $ip = $request->getIPAddress();
        $userAgent = $request->getHeaderLine('User-Agent') ?? 'Desconocido';
        $uri = $request->getUri();
        
        log_message('warning', sprintf(
            'Acceso denegado - IP: %s, Razón: %s, URI: %s, User-Agent: %s',
            $ip,
            $razon,
            $uri,
            $userAgent
        ));
    }

    /**
     * Verifica si la sesión ha expirado (8 horas por defecto)
     */
    private function verificarExpiracionSesion(): void
    {
        $ultimoAcceso = session()->get('ultimo_acceso');
        $tiempoExpiracion = 8 * 3600; // 8 horas en segundos
        
        if ($ultimoAcceso && (time() - $ultimoAcceso) > $tiempoExpiracion) {
            session()->destroy();
            log_message('info', 'Sesión expirada automáticamente');
        }
    }
}
