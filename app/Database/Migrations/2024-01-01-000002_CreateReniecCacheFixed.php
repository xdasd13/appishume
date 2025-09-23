<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migración corregida para crear tabla de cache RENIEC
 * 
 * @author Sistema Ishume
 * @version 1.1
 */
class CreateReniecCacheFixed extends Migration
{
    public function up()
    {
        // Crear tabla reniec_cache con estructura simplificada
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dni' => [
                'type'       => 'CHAR',
                'constraint' => 8,
                'null'       => false,
                'comment'    => 'DNI de 8 dígitos',
            ],
            'nombres' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Nombres completos desde RENIEC',
            ],
            'apellido_paterno' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Apellido paterno desde RENIEC',
            ],
            'apellido_materno' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Apellido materno desde RENIEC',
            ],
            'fecha_nacimiento' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Fecha de nacimiento desde RENIEC',
            ],
            'sexo' => [
                'type'       => 'ENUM',
                'constraint' => ['M', 'F'],
                'null'       => true,
                'comment'    => 'Sexo: M=Masculino, F=Femenino',
            ],
            'estado_civil' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Estado civil desde RENIEC',
            ],
            'ubigeo' => [
                'type'       => 'CHAR',
                'constraint' => 6,
                'null'       => true,
                'comment'    => 'Código de ubigeo',
            ],
            'direccion' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Dirección completa desde RENIEC',
            ],
            'api_response' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Respuesta completa de la API para debugging',
            ],
            'is_valid' => [
                'type'       => 'BOOLEAN',
                'null'       => false,
                'default'    => 1,
                'comment'    => 'Si el DNI es válido según RENIEC',
            ],
            'error_message' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Mensaje de error si la consulta falló',
            ],
            'consulted_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'comment' => 'Fecha y hora de la consulta original',
            ],
            'expires_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'comment' => 'Fecha de expiración del cache (90 días)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Clave primaria
        $this->forge->addKey('id', true);
        
        // Índices
        $this->forge->addUniqueKey('dni');
        $this->forge->addKey('expires_at');
        $this->forge->addKey('is_valid');
        $this->forge->addKey('consulted_at');

        // Crear tabla
        $this->forge->createTable('reniec_cache', true);
    }

    public function down()
    {
        $this->forge->dropTable('reniec_cache');
    }
}
