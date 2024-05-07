<?php

namespace App\Controllers;

// controlador correspondiente a la gestión de empleados por parte de un gerente 
class GestionarEmpleados extends BaseController
{
    public function index()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // si está la sesión creada y es Gerente, saco la vista correspondiente
        if ($session->get("cargo") == 'Gerente') {
            return view("gestionarEmpleados");
        }
        // si es vendedor
        else if ($session->get("cargo") == "Vendedor") {
            return redirect()->to('/');

        } else {
            // si no está la sesión creada, redirijo al login
            return redirect()->to('/login');
        }
    }

    /**
     * Función que devuelve un array con todos los empleados de la base de datos
     * @return string JSON que contiene el array con la cabecera de la tabla y los empleados de la base de datos
     */
    public function obtenerArrayEmpleados()
    {
        // cargo los modelos que necesito
        $empleadosModel = model('Empleados_model');
        $cargosModel = model('Cargos_model');

        // array con todos los empleados para cargarlos en la tabla
        $arrayDatos = array();

        // cabeceras de la tabla
        $arrayDatos['0'] = array(
            'ID',
            'NOMBRE',
            'USERNAME',
            'PASSWORD',
            'CARGO',
            'OPCIONES'
        );

        // objeto con todos los empleados, así puedo guardar en el array el cargo del empleado
        $objetoTodosEmpleados = $empleadosModel->obtenerTodosEmpleados();

        // por cada empleado, creo un array que pushearé al array que devolveré con todos sus datos
        foreach ($objetoTodosEmpleados as $key => $filaTodosEmpleados) {
            $arrayFila = array();

            $arrayFila[0] = $filaTodosEmpleados->id;
            $arrayFila[1] = $filaTodosEmpleados->nombre;
            $arrayFila[2] = $filaTodosEmpleados->user;
            $arrayFila[3] = $filaTodosEmpleados->pass;
            $arrayFila[4] = $cargosModel->obtenerCargoById($filaTodosEmpleados->id_cargo);

            // creo una 6ta posición, la usaré para meter los botones de opciones en la tabla 
            $arrayFila[5] = 'OPCIONES';

            // hago el push
            array_push($arrayDatos, $arrayFila);
        }

        // devuelvo el array de datos 
        return json_encode($arrayDatos);
    }


    /**
     * Función que inserta un empleado en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function insertarEmpleadoBD()
    {
        // cargo el modelo de empleados
        $empleadosModel = model('Empleados_model');

        // obtengo el array de datos que se quieren insertar por POST
        $datosFila = $this->request->getPost('datosFila');

        // los guardo en un array que enviaré al modelo
        $datosEmpleado = array();

        $datosEmpleado['id'] = $datosFila[0];
        $datosEmpleado['nombre'] = $datosFila[1];
        $datosEmpleado['user'] = $datosFila[2];
        $datosEmpleado['pass'] = $datosFila[3];
        $datosEmpleado['id_cargo'] = $datosFila[4];

        // Compruebo si ese id ya existe en la BD
        if ($this->comprobarSiExisteEmpleado($datosEmpleado['id'])) {
            return json_encode(
                array(
                    'status' => 'failed',
                    'message' => 'Ya existe un empleado con ese id en la Base de Datos'
                )
            );

        } else {
            // todas las comprobaciones realizadas, hago la inserción en la base de datos
            // no me hace falta comprobar si existen esa marca y esa categoría ya que se inserta a partir de select option obtenidos directamente de la bd

            // Si devuelve true, la inserción se ha realizado con éxito, de lo contrario ha habido un erro
            if ($empleadosModel->insertarEmpleado($datosEmpleado)) {
                return json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Se ha insertado <b>' . $datosEmpleado['nombre'] . '</b> en la Base de Datos con éxito.'
                    )
                );
            } else {
                return json_encode(
                    array(
                        'status' => 'failed',
                        'message' => 'Ha habido un error insertando el empleado en la base de datos'
                    )
                );
            }
        }
    }

    /**
     * Función que borra un empleado de la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function borrarEmpleadoBD()
    {
        // cargo el modelo de empleados
        $empleadosModel = model('Empleados_model');

        // obtengo el id y el nombre del empleado que se quiere borrar, pasados por POST
        $idEmpleado = $this->request->getPost('idEmpleado');
        $nombreEmpleado = $this->request->getPost('nombreEmpleado');

        // si devuelve true, el borrado se ha realizado con éxito, devuelvo el status y el mensaje correspondiente 
        if ($empleadosModel->borrarEmpleado($idEmpleado)) {
            return json_encode(
                array(
                    'status' => 'success',
                    'message' => 'Se ha borrado el empleado <b>' . $nombreEmpleado . '</b> de la Base de Datos con éxito.'
                )
            );
        } else {
            return json_encode(
                array(
                    'status' => 'failed',
                    'message' => 'Ha habido un error borrando el empleado de la base de datos'
                )
            );
        }
    }

    /**
     * Función que edita un empleado en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function editarEmpleadoBD()
    {
        // cargo el modelo de empleados
        $empleadosModel = model('Empleados_model');

        // obtengo los datos que se quieren editar, pasados por POST
        $datosEditados = $this->request->getPost('datosEditados');

        // los guardo en un array que enviaré al modelo
        $datosEmpleado = array();

        $datosEmpleado['id'] = $datosEditados[0];
        $datosEmpleado['nombre'] = $datosEditados[1];
        $datosEmpleado['user'] = $datosEditados[2];
        $datosEmpleado['pass'] = $datosEditados[3];
        $datosEmpleado['id_cargo'] = $datosEditados[4];

        // no necesito hacer comprobaciones, ya que el id no se puede editar y el cargo se selecciona directamente de un select, cargado de la BD
        // si devuelve true, la edición se realizó con éxito, devuelvo el status y el mensaje correspondientes
        if ($empleadosModel->editarEmpleado($datosEmpleado)) {
            return json_encode(
                array(
                    'status' => 'success',
                    'message' => 'Se ha editado el empleado <b>' . $datosEmpleado['nombre'] . '</b> en la Base de Datos con éxito.'
                )
            );
        } else {
            return json_encode(
                array(
                    'status' => 'failed',
                    'message' => 'Ha habido un error editando el empleado en la base de datos'
                )
            );
        }
    }

    /**
     * Función que obtiene todos los cargos de la base de datos
     * @return string JSON que contiene el array con todos los cargos de la base de datos
     */
    public function obtenerArrayCargos()
    {
        // cargo el modelo de cargos
        $cargosModel = model('Cargos_model');

        // devuelvo el array con todos los cargos de la base de datos
        return json_encode($cargosModel->obtenerTodosCargos());
    }


    /**
     * Función que comprueba si existe un empleado a partir de una id
     * @return boolean Boolean que indica si ese id de empleado ya existe
     */
    public function comprobarSiExisteEmpleado($idEmpleado)
    {
        // cargo el modelo de empleados
        $empleadosModel = model('Empleados_model');

        // devuelvo un booleano que indica si ese id de empleado ya existe
        return $empleadosModel->obtenerEmpleadoById($idEmpleado);
    }

}
