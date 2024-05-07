<?php

namespace App\Controllers;

use App\Models\Productos_model;

// controlador correspondiente a la visión de los movimientos por parte de un gerente 
class Movimientos extends BaseController
{
    public function index()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // si está la sesión creada y es Gerente, saco la vista correspondiente
        if ($session->get("cargo") == 'Gerente') {
            return view("movimientos");
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
     * Función que devuelve un array con todos los movimientos de la base de datos
     * @return string JSON que contiene el array con la cabecera de la tabla y los movimientos de la base de datos
     */
    public function obtenerArrayMovimientos()
    {
        // cargo los modelos que necesito
        $pedidosModel = model('Pedidos_model');
        // $detallesPedidoModel = model('DetallesPedido_Model');
        $empleadosModel = model('Empleados_model');
        // $Productos_model = model('Productos_model');

        // array con todos los empleados para cargarlos en la tabla
        $arrayDatos = array();

        // cabeceras de la tabla
        $arrayDatos['0'] = array(
            'ID PEDIDO',
            'VENDEDOR',
            'PRECIO TOTAL',
            'HORA',
            'OPCIONES'
        );

        // objeto con todos los empleados, así puedo guardar en el array el cargo del empleado
        $objetoTodosPedidos = $pedidosModel->obtenerTodosPedidos();

        // por cada empleado, creo un array que pushearé al array que devolveré con todos sus datos
        foreach ($objetoTodosPedidos as $key => $filaTodosEmpleados) {
            $arrayFila = array();

            $arrayFila[0] = $filaTodosEmpleados->id;
            // obtengo el vendedor
            $arrayFila[1] = $empleadosModel->obtenerNombreEmpleadoById($filaTodosEmpleados->id_vendedor);
            $arrayFila[2] = $filaTodosEmpleados->precio_total;
            $arrayFila[3] = $filaTodosEmpleados->hora;

            // creo una 5ta posición, la usaré para meter los botones de opciones en la tabla 
            $arrayFila[4] = 'OPCIONES';

            // hago el push
            array_push($arrayDatos, $arrayFila);
        }

        // devuelvo el array de datos 
        return json_encode($arrayDatos);
    }


    /**
     * Función que devuelve los detalles de un pedido pasado por POST
     * @return string JSON que contiene todos los datos de los detalles de un pedido
     */
    public function obtenerDetallesPedido()
    {
        // obtengo el id del pedido pasado por POST
        $idPedido = $this->request->getPost('idPedido');

        // cargo los modelos que necesito
        $detallesPedidoModel = model('DetallesPedido_model');
        $productosModel = model('Productos_model');
        $marcasModel = model('Marcas_model');

        // array con los datos de los detalles del pedido
        $arrayFinal = array();

        $detallesRespuesta = $detallesPedidoModel->obtenerDetallesPedidoByIdPedido($idPedido);

        foreach ($detallesRespuesta as $key => $detalle) {
            $arrayLinea[0] = $detalle->id;
            // guardo el nombre del producto
            $arrayLinea[1] = $productosModel->obtenerNombreProductoById($detalle->id_producto);

            // guardo la marca del producto
            $idMarcaProducto = $productosModel->obtenerMarcaProductoById($detalle->id_producto);
            $arrayLinea[2] = $marcasModel->obtenerMarcaById($idMarcaProducto);

            $arrayLinea[3] = $detalle->cantidad;

            array_push($arrayFinal, $arrayLinea);
        }
        // devuelvo el array con todos los detalles
        return json_encode($arrayFinal);
    }



}
