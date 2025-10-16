<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Database Configuration for Remote Server
    |--------------------------------------------------------------------------
    |
    | Configuración para conectar con la base de datos remota de freesqldatabase.com
    | Usar esta configuración para pruebas con compañeros
    |
    */

    'default' => [
        'hostname' => 'sql10.freesqldatabase.com',
        'username' => 'sql10803359',
        'password' => 'zte6S1BQq5', // Cambiar por la contraseña real
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
    | Instrucciones de Uso
    |--------------------------------------------------------------------------
    |
    | 1. Obtén la contraseña de tu cuenta en freesqldatabase.com
    | 2. Reemplaza 'TU_CONTRASEÑA_AQUI' con la contraseña real
    | 3. Copia este archivo a app/Config/Database.php
    | 4. Ejecuta los scripts SQL para crear las tablas
    | 5. Comparte esta configuración con tus compañeros
    |
    */
];
