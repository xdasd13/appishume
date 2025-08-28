<?php

namespace App\Models;

use CodeIgniter\Model;

class ServiciosContratadosModel extends Model
{
    protected $table = 'servicioscontratados';
    protected $primaryKey = 'idserviciocontratado';
    protected $allowedFields = ['idcotizacion', 'idservicio', 'cantidad', 'precio', 'fechahoraservicio', 'direccion'];
    protected $returnType = 'array';
}