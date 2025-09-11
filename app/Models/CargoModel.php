<?php

namespace App\Models;

use CodeIgniter\Model;

class CargoModel extends Model
{
    protected $table = 'cargos';
    protected $primaryKey = 'idcargo';
    protected $allowedFields = ['cargo'];
    protected $returnType = 'object'; // Forzar que devuelva objetos
}
