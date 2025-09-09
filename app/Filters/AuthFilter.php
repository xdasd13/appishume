<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar si el usuario está logueado
        if (!session()->get('usuario_logueado')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión para acceder.');
        }

        // Si se especifica un tipo de usuario requerido
        if (!empty($arguments)) {
            $tipoRequerido = $arguments[0];
            $tipoUsuario = session()->get('usuario_tipo');

            if ($tipoUsuario !== $tipoRequerido) {
                return redirect()->to('/login')->with('error', 'No tienes permisos para acceder a esta sección.');
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No se necesita procesamiento posterior
    }
}
