<?php

namespace App\Models;

use CodeIgniter\Model;

// modelo correspondiente al a tabla "categorías"
class Categorias_model extends Model
{
    // defino el nombre de la tabla
    protected $table = "categorias";

    /**
     * Función que obtiene el nombre de una categoría a partir de su id
     * @param int $idCategoria id de la categoría que se quiere obtener
     * @return string string con el nombre de esa categoría
     */
    public function obtenerCategoriaById($idCategoria)
    {
        // cargo el builder de la tabla categorias
        $builder = $this->db->table($this->table);

        // devuelvo el nombre de la categoría con ese id
        $builder->select('nombre');
        $builder->where('id', $idCategoria);

        // devuelvo el nombre de la categoría
        $row = $builder->get()->getRow();
        return $row->nombre;
    }

    /**
     * Función que devuelve todas las categorías de la base de datos
     * @return array Array con todos los datos de todas las categorías de la base de datos
     */
    public function obtenerTodasCategorias()
    {
        // cargo el builder de la tabla categorias
        $builder = $this->db->table($this->table);

        $builder->select('*');

        // devuelvo el resultado
        return $builder->get()->getResultArray();
    }

    /**
     * Función que inserta una categoría en la base de datos
     * @param int $id id de la categoría que se quiere insertar
     * @param int $nombre nombre de la categoría que se quiere insertar
     * @return boolean que indicará la acción se realizó con éxito o no
     */
    public function insertarCategoria($id, $nombre)
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
     * Función que borra una categoría de la base de datos
     * @param int $id id de la categoría que se quiere borrar
     * @return boolean que indicará la acción se realizó con éxito o no
     */
    public function borrarCategoria($id)
    {
        // cargo el builder de la tabla categorias
        $builder = $this->db->table($this->table);

        // borro la categoría que coincide con el id pasado por parámetro
        $builder->where('id', $id);

        // si se borra con éxito, devuelvo true, si no false
        if ($builder->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que edita los datos de la categoría cuya id coincide con la pasada por parámetro
     * @param int $id id de la categoría que se quiere editar
     * @param string $nuevoNombre nuevo nombre que tendrá la categoría
     * @return boolean que indicará la acción se realizó con éxito o no
     */
    public function editarCategoria($id, $nuevoNombre)
    {
        $data = array(
            'nombre' => $nuevoNombre
        );

        // cargo el builder de la tabla categorías
        $builder = $this->db->table($this->table);

        // edito la categoría que coincide con el id pasado por parámetro
        $builder->where('id', $id);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que comprueba si existe una categoría con ese id
     * @param string $id id de la categoría que se quiere comprobar
     * @return boolean que indica si existe o no
     */
    public function comprobarSiExisteCategoria($id)
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
     * Función que comprueba si existe una categoría con ese nombre
     * @param string $nombre nombre de la categoría que se quiere comprobar
     * @return boolean que indica si existe o no
     */
    public function comprobarSiExisteCategoriaByNombre($nombre)
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



}
