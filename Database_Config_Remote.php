<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Configuration for ISHUME
    |--------------------------------------------------------------------------
    |
    | Configuración para conectar con la base de datos remota
    | Host: sql10.freesqldatabase.com
    | Database: sql10803359
    | Username: sql10803359
    |
    */

    'default' => [
        'hostname' => 'sql10.freesqldatabase.com',
        'username' => 'sql10803359',
        'password' => 'zte6S1BQq5', // ⚠️ CAMBIAR POR LA CONTRASEÑA REAL
        'database' => 'sql10803359',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => true,
        'charset' => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'saveQueries' => true,
        'port' => 3306,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración Local (para desarrollo)
    |--------------------------------------------------------------------------
    |
    | Usar esta configuración para desarrollo local con XAMPP
    |
    */
    'local' => [
        'hostname' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'ishume',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug' => true,
        'charset' => 'utf8',
        'DBCollat' => 'utf8_general_ci',
        'swapPre' => '',
        'encrypt' => false,
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'saveQueries' => true,
        'port' => 3306,
    ],

    /*
    |--------------------------------------------------------------------------
    | Instrucciones de Configuración
    |--------------------------------------------------------------------------
    |
    | 1. Obtén la contraseña de tu cuenta en freesqldatabase.com
    | 2. Reemplaza 'TU_CONTRASEÑA_AQUI' con la contraseña real
    | 3. Copia este archivo a app/Config/Database.php
    | 4. Ejecuta el script database_completo_remote.sql en HeidiSQL
    | 5. Comparte esta configuración con tus compañeros
    |
    | Para cambiar entre base de datos local y remota:
    | - Cambia 'default' por 'local' para usar XAMPP
    | - Cambia 'local' por 'default' para usar servidor remoto
    |
    */
];
