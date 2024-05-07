<?php

namespace App\Controllers;

// controlador correspondiente a la configuración de usuarios
class ConfiguracionUsuario extends BaseController
{
    // declaro $sessión para quitar el error de variable no definida, aun que lo esté en el contructor
    protected $session;

    public function __construct()
    {
        // defino session para poderlo usar en todos los métodos
        $this->session = \Config\Services::session();
    }


    public function index()
    {
        // si está la sesión creada, saco la vista del menu
        if ($this->session->get("user")) {
            return view("configuracionUsuario");

        } else {
            // si no está la sesión creada, redirijo al login
            return redirect()->to('http://localhost:8080/login');
        }
    }

    /**
     * Función que cambia el nombre de usuario de un empleado en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function cambiarUsernameBD()
    {
        // guardo los datos pasados por post
        $nuevoUsername = $this->request->getPost('nuevoUsername');
        $password = $this->request->getPost('password');

        // obtengo el nombre de usuario guardado en una sesión
        $user = $this->session->get('user');

        // cargo el modelo de Empleados
        $empleadosModel = model('Empleados_model');

        // si las credenciales son incorrectas, devuelvo el error para notificarlo en el diálogo de JQuery UI
        if (!$empleadosModel->comprobarCredenciales($user, $password)) {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'La <b>contraseña</b> es <b>incorrecta</b>'
                )
            );
        }
        // no hay más comprobaciones que hacer, cambio el nombre de usuario en la BD
        else {
            // devuelvo success o error y el mensaje que se sacará en la alerta
            if ($empleadosModel->cambiarUsernameByUser($user, $nuevoUsername)) {
                return json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Se ha <b>cambiado el nombre de usuario</b> con éxito. Se cerrará la sesión'
                    )
                );
            } else {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Ha habido un problema cambiando tu contraseña'
                    )
                );
            }
        }

    }
    /**
     * Función que cambia el nombre y apellidos de un empleado en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function cambiarNombreBD()
    {
        // guardo los datos pasados por post
        $nuevoNombre = $this->request->getPost('nuevoNombre');
        $password = $this->request->getPost('password');

        // obtengo el nombre de usuario guardado en una sesión
        $user = $this->session->get('user');

        // cargo el modelo de Empleados
        $empleadosModel = model('Empleados_model');

        // si las credenciales son incorrectas, devuelvo el error para notificarlo en el diálogo de JQuery UI
        if (!$empleadosModel->comprobarCredenciales($user, $password)) {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'La <b>contraseña</b> es <b>incorrecta</b>'
                )
            );
        }
        // no hay más comprobaciones que hacer, cambio el nombre de usuario en la BD
        else {
            // devuelvo succes o error y el mensaje que saldrá en la alerta
            if ($empleadosModel->cambiarNombreByUser($user, $nuevoNombre)) {
                return json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Se ha <b>cambiado el nombre y apellidos</b> con éxito. Se cerrará la sesión'
                    )
                );
            } else {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Ha habido un problema cambiando tu nombre y apellidos'
                    )
                );
            }
        }

    }


    /**
     * Función que cambia la contraseña de un empleado en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function cambiarPasswordBD()
    {
        // guardo los datos pasados por post
        $passwordActual = $this->request->getPost('passwordActual');
        $passwordNueva = $this->request->getPost('passwordNueva');
        $passwordNuevaRepetir = $this->request->getPost('passwordNuevaRepetir');

        // obtengo el nombre de usuario guardado en una sesión
        $user = $this->session->get('user');

        // cargo el modelo de Empleados
        $empleadosModel = model('Empleados_model');

        // si las credenciales son incorrectas, devuelvo el error para notificarlo en el diálogo de JQuery UI
        if (!$empleadosModel->comprobarCredenciales($user, $passwordActual)) {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'La <b>contraseña actual</b> es <b>incorrecta</b>'
                )
            );
        }
        // sigo haciendo comprobaciones antes de cambiar la contraseña en la BD 
        else {
            // si la nueva contraseña no coincide en ambos inputs, devuelvo el error para notificarlo
            if ($passwordNueva != $passwordNuevaRepetir) {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'La <b>nueva contraseña no coincide</b> en ambos inputs'
                    )
                );
            }
            // todas las validaciones hechas, puedo cambiar la contraseña en la BD
            else {
                // devuelvo success o error y el mensaje que saldrá en la alerta
                if ($empleadosModel->cambiarPasswordByUser($user, $passwordNueva)) {
                    return json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Se ha <b>cambiado la contraseña</b> con éxito. Se cerrará la sesión'
                        )
                    );
                } else {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ha habido un problema cambiando tu contraseña'
                        )
                    );
                }
            }
        }
    }
}



