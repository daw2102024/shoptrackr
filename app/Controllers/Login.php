<?php

namespace App\Controllers;

// controlador correspondiente al login
class Login extends BaseController
{

    public function index()
    {
        // devuelvo la vista del login
        return view('login');
    }

    /**
     * Función que comprueba las credenciales de los input, y crea la sesión si son correctas
     * @return boolean Boolean que indica si el login fue correcto o no
     */
    public function login()
    {
        // obtengo los valores de los inputs por POST
        $user = $this->request->getPost('user');
        $pass = $this->request->getPost('pass');

        // cargo los modelos que necesitaré 
        $empleadosModel = model('Empleados_model');
        $cargosModel = model('Cargos_model');

        // compruebo las credenciales
        $status = $empleadosModel->comprobarCredenciales($user, $pass);

        // si las credenciales son correctas, creo la sesión
        if ($status == true) {

            // inicializo el servicio de session
            $session = \Config\Services::session();

            // sesión con el nombre de usuario
            $session->set('user', $user);

            // sesión con el nombre del empleado
            $session->set('nombre', $empleadosModel->obtenerNombreByUser($user));

            // obtengo el cargo del empleado
            $idCargo = $empleadosModel->obtenerCargoByUser($user);
            // sesión con el cargo del empleado (Gerente o Vendedor)
            $session->set('cargo', $cargosModel->obtenerCargoById($idCargo));

            // guardo también el id del usuario
            $session->set('idUser', $empleadosModel->obtenerIdByUser($user));

            // devuelvo el status (login correcto)
            return json_encode($status);

        } else {
            // devuelvo el status (login incorrecto)
            return json_encode($status);
        }

    }
}
