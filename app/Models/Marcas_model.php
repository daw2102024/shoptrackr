<?php

namespace App\Models;

use CodeIgniter\Model;

// modelo correspondiente a la tabla "marcas"
class Marcas_model extends Model
{

    // defino el nombre de la tabla
    protected $table = "marcas";

    /**
     * Función que devuelve el nombre de la marca a partir de su id
     * @param string $idMarca id de la marca de la que se quiere obtener el nombre de la marca
     * @return string nombre de la marca
     */
    public function obtenerMarcaById($idMarca)
    {
        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        // obtengo el nombre que coincide con ese id
        $builder->select('nombre');
        $builder->where('id', $idMarca);

        // devuelvo el nombre de la marca
        $row = $builder->get()->getRow();
        return $row->nombre;
    }

    /**
     * Función que comprueba si existe una marca con ese id
     * @param string $id id de la marca que se quiere comprobar
     * @return boolean que indica si existe o no
     */
    public function comprobarSiExisteMarca($id)
    {
        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        // busco por el id pasado por parámetro
        $builder->where('id', $id);

        // obtengo el número de resultados obtenidos
        $count = $builder->countAllResults();

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Función que comprueba si existe una marca con ese nombre
     * @param string $nombre id de la marca que se quiere comprobar
     * @return boolean que indica si existe o no
     */
    public function comprobarSiExisteMarcaByNombre($nombre)
    {
        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        // busco por el id pasado por parámetro
        $builder->where('nombre', $nombre);

        // obtengo el número de resultados obtenidos
        $count = $builder->countAllResults();

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Función que devuelve todas las marcas cargadas en la base de datos
     * @return array con todas las marcas cargadas en la base de datos
     */
    public function obtenerTodasMarcas()
    {
        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        $builder->select('*');

        // devuelvo el resultado de la query
        return $builder->get()->getResult();

    }

    /**
     * Función que inserta una marca en la base de datos
     * @param int $id id de la marca que se quiere insertar
     * @param int $nombre nombre de la marca que se quiere insertar
     * @return boolean que indicará la acción se realizó con éxito o no
     */
    public function insertarMarca($id, $nombre)
    {
        $data = [
            'id' => $id,
            'nombre' => $nombre
        ];

        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        // si se interta con éxito, devuelve  true, si no false
        if ($builder->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que borra una marca de la base de datos
     * @param int $id id de la marca que se quiere borrar
     * @return boolean que indicará la acción se realizó con éxito o no
     */
    public function borrarMarca($id)
    {
        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        // borro la marca que coincide con el id pasado por parámetro
        $builder->where('id', $id);

        // si se borra con éxito, devuelvo true, si no false
        if ($builder->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que edita los datos de la marca cuya id coincide con la pasada por parámetro
     * @param int $id id de la marca que se quiere editar
     * @param string $nuevoNombre nuevo nombre que tendrá la marca
     * @return boolean que indicará la acción se realizó con éxito o no
     */
    public function editarMarca($id, $nuevoNombre)
    {
        $data = array(
            'nombre' => $nuevoNombre
        );

        // cargo el builder de la tabla marcas
        $builder = $this->db->table($this->table);

        // edito la marca que coincide con el id pasado por parámetro
        $builder->where('id', $id);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }
}
