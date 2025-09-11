<?php

namespace App\Models;

use CodeIgniter\Model;

class PersonaModel extends Model
{
    protected $table = 'personas';
    protected $primaryKey = 'idpersona';
    protected $allowedFields = [
        'apellidos', 'nombres', 'tipodoc', 'numerodoc', 
        'telprincipal', 'telalternativo', 'direccion', 'referencia'
    ];

    // Obtener personas que no tienen usuario asignado
    public function getPersonasSinUsuario()
    {
        $builder = $this->db->table('personas p');
        $builder->select('p.idpersona, p.nombres, p.apellidos, p.numerodoc, p.tipodoc, 
                         COALESCE(p.telprincipal, "") as telprincipal, 
                         COALESCE(p.telalternativo, "") as telalternativo, 
                         COALESCE(p.direccion, "") as direccion, 
                         COALESCE(p.referencia, "") as referencia');
        $builder->join('usuarios u', 'p.idpersona = u.idpersona', 'left');
        $builder->where('u.idpersona IS NULL');
        $builder->orderBy('p.apellidos, p.nombres');
        return $builder->get()->getResult();
    }

    // Obtener todas las personas
    public function getPersonas()
    {
        $builder = $this->db->table('personas');
        $builder->orderBy('apellidos, nombres');
        return $builder->get()->getResult();
    }
}
