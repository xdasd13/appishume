<?php
/**
 * Script de verificación del sistema RENIEC
 * Ejecutar desde línea de comandos: php verificar_sistema.php
 */

echo "🔧 VERIFICACIÓN DEL SISTEMA RENIEC ISHUME\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];

// 1. Verificar PHP y extensiones
echo "📋 1. Verificando PHP y extensiones...\n";
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    $errors[] = "PHP 8.0+ requerido. Versión actual: " . PHP_VERSION;
} else {
    echo "✅ PHP " . PHP_VERSION . " - OK\n";
}

$required_extensions = ['curl', 'json', 'openssl', 'mysqli'];
foreach ($required_extensions as $ext) {
    if (!extension_loaded($ext)) {
        $errors[] = "Extensión PHP '$ext' no está habilitada";
    } else {
        echo "✅ Extensión $ext - OK\n";
    }
}

// 2. Verificar archivos esenciales
echo "\n📁 2. Verificando archivos esenciales...\n";
$required_files = [
    'app/Libraries/ReniecService.php',
    'app/Models/ReniecCacheModel.php',
    'app/Controllers/UsuariosController.php',
    'app/Views/usuarios/crear.php',
    'app/Database/Migrations/2024-01-01-000002_CreateReniecCacheFixed.php',
    '.env'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        $errors[] = "Archivo requerido no encontrado: $file";
    } else {
        echo "✅ $file - OK\n";
    }
}

// 3. Verificar configuración .env
echo "\n⚙️ 3. Verificando configuración .env...\n";
if (file_exists('.env')) {
    $env_content = file_get_contents('.env');
    
    $env_checks = [
        'database.default.hostname' => 'Hostname de base de datos',
        'database.default.database' => 'Nombre de base de datos',
        'database.default.username' => 'Usuario de base de datos',
        'DECOLECTA_API_TOKEN' => 'Token de API Decolecta',
        'DECOLECTA_API_URL' => 'URL de API Decolecta'
    ];
    
    foreach ($env_checks as $key => $description) {
        if (strpos($env_content, $key) === false) {
            $warnings[] = "Configuración '$key' no encontrada en .env ($description)";
        } else {
            echo "✅ $key configurado\n";
        }
    }
} else {
    $errors[] = "Archivo .env no encontrado. Copiar desde .env.example";
}

// 4. Verificar permisos de escritura
echo "\n🔐 4. Verificando permisos...\n";
$writable_dirs = ['writable', 'writable/logs', 'writable/cache', 'writable/session'];
foreach ($writable_dirs as $dir) {
    if (!is_dir($dir)) {
        $warnings[] = "Directorio no encontrado: $dir";
    } elseif (!is_writable($dir)) {
        $errors[] = "Directorio sin permisos de escritura: $dir";
    } else {
        echo "✅ $dir - Escribible\n";
    }
}

// 5. Test de conectividad (si es posible)
echo "\n🌐 5. Verificando conectividad...\n";
if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://httpbin.org/get');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response && $http_code === 200) {
        echo "✅ Conectividad a internet - OK\n";
    } else {
        $warnings[] = "Problemas de conectividad a internet";
    }
} else {
    $errors[] = "cURL no disponible para test de conectividad";
}

// Resumen final
echo "\n" . str_repeat("=", 50) . "\n";
echo "📊 RESUMEN DE VERIFICACIÓN\n";
echo str_repeat("=", 50) . "\n";

if (empty($errors) && empty($warnings)) {
    echo "🎉 ¡SISTEMA COMPLETAMENTE VERIFICADO!\n";
    echo "✅ Todos los componentes están listos\n";
    echo "✅ El sistema RENIEC debería funcionar correctamente\n\n";
    echo "🚀 PRÓXIMOS PASOS:\n";
    echo "1. Ejecutar: php spark migrate\n";
    echo "2. Acceder a: http://localhost/ishume\n";
    echo "3. Login: admin@ishume.com / admin123\n";
    echo "4. Probar creación de usuario con DNI: 60752963\n";
} else {
    if (!empty($errors)) {
        echo "❌ ERRORES CRÍTICOS ENCONTRADOS:\n";
        foreach ($errors as $error) {
            echo "   • $error\n";
        }
        echo "\n";
    }
    
    if (!empty($warnings)) {
        echo "⚠️ ADVERTENCIAS:\n";
        foreach ($warnings as $warning) {
            echo "   • $warning\n";
        }
        echo "\n";
    }
    
    echo "🔧 SOLUCIONAR ESTOS PROBLEMAS ANTES DE CONTINUAR\n";
}

echo "\n📖 Para más información, consulta: GUIA_INSTALACION_RENIEC.md\n";
echo "🐛 Si necesitas ayuda, incluye la salida de este script\n\n";
?>
