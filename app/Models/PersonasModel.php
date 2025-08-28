<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonasModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'idpersona';
    protected $allowedFields = ['apellidos', 'nombres', 'tipodoc', 'numerodoc', 'telprincipal', 'telalternativo', 'direccion', 'referencia'];
    protected $returnType = 'array';
}