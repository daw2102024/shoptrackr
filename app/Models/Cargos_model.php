<?php

namespace App\Models;

use CodeIgniter\Model;

// modelo correspondiente a la tabla "cargos"
class Cargos_model extends Model
{

    // defino el nombre de la tabla
    protected $table = "cargos";

    /**
     * Función que devuelve el nombre del cargo por su id
     * @param int $idCargo id del cargo que se quiere obtener
     * @return string string con el nombre del cargo
     */
    public function obtenerCargoById($idCargo)
    {
        // cargo el builder de la tabla cargos
        $builder = $this->db->table($this->table);

        // utilizo el query builder
        $builder->select('nombre');
        $builder->where('id', $idCargo);

        // devuelvo el nombre del cargo
        $row = $builder->get()->getRow();

        return $row->nombre;
    }

    /**
     * Función que devuelve todos los cargos de la base de datos
     * @return array Array con todo los cargos de la base de datos
     */
    public function obtenerTodosCargos()
    {
        // cargo el builder de la tabla cargos
        $builder = $this->db->table($this->table);

        // selecciono todos
        $builder->select('*');

        // devuelvo el array con el resultado de la query
        return $builder->get()->getResultArray();

    }

}
