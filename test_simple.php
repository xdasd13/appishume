<?php
// Test simple para verificar que el servidor funciona
echo "<!DOCTYPE html>";
echo "<html><head><title>Test Simple</title></head><body>";
echo "<h1>Test de Funcionamiento</h1>";
echo "<p>Si ves esto, el servidor PHP funciona correctamente.</p>";
echo "<p>Fecha y hora: " . date('Y-m-d H:i:s') . "</p>";

// Test de base de datos
try {
    $db = new PDO('mysql:host=localhost;dbname=ishume', 'root', '');
    echo "<p style='color: green;'>✅ Conexión a base de datos: EXITOSA</p>";
    
    // Test de tabla usuarios
    $stmt = $db->query("SELECT COUNT(*) as total FROM usuarios");
    $result = $stmt->fetch();
    echo "<p>Total usuarios en BD: " . $result['total'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error de base de datos: " . $e->getMessage() . "</p>";
}

// Test de jQuery
echo "<script src='https://code.jquery.com/jquery-3.7.1.min.js'></script>";
echo "<script>";
echo "$(document).ready(function() {";
echo "  console.log('jQuery funciona correctamente');";
echo "  $('body').append('<p style=\"color: blue;\">✅ jQuery cargado correctamente</p>');";
echo "});";
echo "</script>";

echo "</body></html>";
?>
