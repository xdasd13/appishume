<?php
// Test directo del controlador de usuarios
require_once 'vendor/autoload.php';

// Configuración básica
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/test';

// Inicializar CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "<!DOCTYPE html>";
echo "<html><head><title>Test Usuarios</title>";
echo "<script src='https://code.jquery.com/jquery-3.7.1.min.js'></script>";
echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
echo "</head><body>";

echo "<h1>Test Directo - Módulo Usuarios</h1>";

// Test 1: Verificar que el controlador existe
try {
    $controller = new \App\Controllers\UsuariosController();
    echo "<p style='color: green;'>✅ UsuariosController cargado correctamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error cargando UsuariosController: " . $e->getMessage() . "</p>";
}

// Test 2: Verificar modelos
try {
    $usuarioModel = new \App\Models\UsuarioModel();
    echo "<p style='color: green;'>✅ UsuarioModel cargado correctamente</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error cargando UsuarioModel: " . $e->getMessage() . "</p>";
}

// Test 3: Formulario simple de prueba
echo "<hr>";
echo "<h2>Formulario de Prueba Simple</h2>";
echo "<form id='test-form'>";
echo "<input type='text' id='test-input' placeholder='Escribe algo...'>";
echo "<button type='submit'>Enviar</button>";
echo "</form>";

echo "<div id='resultado'></div>";

// JavaScript de prueba
echo "<script>";
echo "$(document).ready(function() {";
echo "  console.log('Test: jQuery funcionando');";
echo "  $('#test-form').submit(function(e) {";
echo "    e.preventDefault();";
echo "    console.log('Test: Formulario enviado');";
echo "    var valor = $('#test-input').val();";
echo "    $('#resultado').html('<p>Valor recibido: ' + valor + '</p>');";
echo "    Swal.fire('Éxito', 'Formulario funciona correctamente', 'success');";
echo "  });";
echo "});";
echo "</script>";

echo "</body></html>";
?>
