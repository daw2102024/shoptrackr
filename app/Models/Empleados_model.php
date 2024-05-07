<?php

namespace App\Models;

use CodeIgniter\Model;

// controlador correspondiente a la tabla "empleados"
class Empleados_model extends Model
{
    // defino el nombre de la tabla
    protected $table = "empleados";

    /**
     * Función que devuelve un objeto con todos los empleados de la base de datos
     * @return array Array con todos los datos de todos los empleados de la base de datos
     */
    public function obtenerTodosEmpleados()
    {
        // cargo el builder de la tabla empleados
        $builder = $this->db->table($this->table);

        $builder->select('*');

        // devuelvo el resultado
        return $builder->get()->getResult();
    }

    /**
     * Función que devuelve el el id del usuario a partir de su nombre de usuario
     * @param int $user Nombre de usuario del empleado
     * @return int id del empleado
     */
    public function obtenerIdByUser($user)
    {
        // cargo el builder de la tabla empleados
        $builder = $this->db->table($this->table);

        // devuelvo todos los datos del empleado que coincide con ese id
        $builder->select('id');
        $builder->where('user', $user);

        $row = $builder->get()->getRow();

        // devuelvo el id
        return $row->id;
    }

    /**
     * Función que devuelve el empleado que coincide con una id
     * @param int $idEmpleado Id del empleado que se quiere obtener
     * @return boolean que indica si existe un empleado con ese id
     */
    public function obtenerEmpleadoById($idEmpleado)
    {
        // cargo el builder de la tabla empleados
        $builder = $this->db->table($this->table);

        // devuelvo todos los datos del empleado que coincide con ese id
        $builder->select('*');
        $builder->where('id', $idEmpleado);

        // si existe un empleado con ese id, devuelvo true
        if ($builder->get()->getResult()) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Función que devuelve el nombre del empleado a partir de su id
     * @param int $idEmpleado Id del empleado que se quiere obtener
     * @return string nombre del empleado correspondiente a ese id
     */
    public function obtenerNombreEmpleadoById($idEmpleado)
    {
        // cargo el builder de la tabla empleados
        $builder = $this->db->table($this->table);

        // devuelvo todos los datos del empleado que coincide con ese id
        $builder->select('*');
        $builder->where('id', $idEmpleado);
        $row = $builder->get()->getRow();

        // devuelvo el nombre
        return $row->nombre;
    }


    /**
     * Función que comprueba si la contraseña pasada por parámetro coincide con la contraseña del usuario pasado por parámetro
     * @param string $user nombre de usuario del que se quiere comprobar las credenciales
     * @param string $pass contraseña que se quiere comprobar
     * @return boolean que indica si las credenciales con correctas 
     */
    public function comprobarCredenciales($user, $pass)
    {

        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);


        $builder->select('*');
        $builder->where('user', $user);

        $row = $builder->get()->getRow();

        // si hay un usuario en la base de datos con ese nombre
        if ($row != null) {
            // si las contraseñas coinciden devuelvo true, si no false
            if ($row->pass == $pass) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    /**
     * Función que obtiene el cargo de un empleado a partir del nombre de usuario
     * @param string $user nombre de usuario del empleado del que se quiere obtener el id de cargo
     * @return string id del cargo que tiene ese usuario
     */
    public function obtenerCargoByUser($user)
    {
        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        $builder->select('id_cargo');
        $builder->where('user', $user);

        $row = $builder->get()->getRow();

        // devuelvo el id_cargo
        return $row->id_cargo;

    }

    /**
     * Función que obtiene el nombre del empleado a partir del nombre de usuario
     * @param string $user nombre de usuario del empleado del que se quiere saber el nombre
     * @return string nombre del empleado que coincide con ese nombre de usuario
     */
    public function obtenerNombreByUser($user)
    {
        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        $builder->select('nombre');
        $builder->where('user', $user);

        $row = $builder->get()->getRow();

        // devuelvo el nombre del emplado
        return $row->nombre;
    }

    /**
     * Función que cambia la contraseña de un empleado en la BD
     * @param string $user nombre de usuario del empleado
     * @param string $passwordNueva nueva contraseña del empleado
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function cambiarPasswordByUser($user, $passwordNueva)
    {
        // array con los datos que se quieren modificar
        $data = array(
            'pass' => $passwordNueva
        );

        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        // para el usuario con ese nombre de usuario, hago el update con la nueva contraseña
        $builder->where('user', $user);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Función que cambia el nombre de usuario de un empleado en la BD
     * @param string $user nombre de usuario del empleado
     * @param string $user nuevo nombre de usuario del empleado
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function cambiarUsernameByUser($user, $nuevoUsername)
    {
        // array con los cambios que se quieren realizar
        $data = array(
            'user' => $nuevoUsername
        );

        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        // para el usuario con ese nombre de usuario, hago el update con la nueva contraseña
        $builder->where('user', $user);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Función que cambia el nombre de un empleado en la BD
     * @param string $user nombre de usuario del empleado
     * @param string $nuevoNombre nuevo nombre de usuario del empleado
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function cambiarNombreByUser($user, $nuevoNombre)
    {
        // array con los cambios que se quieren realizar
        $data = array(
            'nombre' => $nuevoNombre
        );

        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        // para el usuario con ese nombre de usuario, hago el update con la nueva contraseña
        $builder->where('user', $user);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que inserta un nuevo empleado en la base de datos
     * @param array $datosEmpleado datos del empleado que se quiere insertar
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function insertarEmpleado($datosEmpleado)
    {
        // array con los datos del empleado que se quiere insertar
        $data = [
            'id' => $datosEmpleado['id'],
            'nombre' => $datosEmpleado['nombre'],
            'user' => $datosEmpleado['user'],
            'pass' => $datosEmpleado['pass'],
            'id_cargo' => $datosEmpleado['id_cargo']
        ];

        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        // si se interta con éxito, devuelve  true, si no false
        if ($builder->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que borra un empleado de la base de datos
     * @param string $idEmpleado id del empleado que se quiere borrar de la base de datos
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function borrarEmpleado($idEmpleado)
    {
        // cargo el builder de la tabla empleados
        $builder = $this->db->table($this->table);

        // borro el empleado con ese id
        $builder->where('id', $idEmpleado);

        // si se borra con éxito, devuelvo true, si no false
        if ($builder->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que edita un empleado en la base de datos
     * @param array $datosEmpleado datos del empleado que se quiere editar
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function editarEmpleado($datosEmpleado)
    {
        // array con los datos del empleado que se quiere editar
        $data = array(
            'nombre' => $datosEmpleado['nombre'],
            'user' => $datosEmpleado['user'],
            'pass' => $datosEmpleado['pass'],
            'id_cargo' => $datosEmpleado['id_cargo']
        );

        // cargo el builder con la tabla de empleados
        $builder = $this->db->table($this->table);

        // para el usuario con ese id, hago el update con la nueva contraseña
        $builder->where('id', $datosEmpleado['id']);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }
}
