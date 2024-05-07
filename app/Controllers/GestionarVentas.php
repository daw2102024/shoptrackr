<?php

namespace App\Controllers;

// controlador correspondiente a la gestión de productos
class GestionarVentas extends BaseController
{
    public function index()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // si está la sesión creada, saco la vista del menu
        if ($session->get("user")) {
            return view("gestionarVentas");

        } else {
            // si no está la sesión creada, redirijo al login
            return redirect()->to('/login');
        }

    }


    /**
     * Función que devuelve un array con la cabeceras y todos los productos de la base de datos
     * @return string JSON que contiene el array con esos datos
     */
    public function obtenerArrayProductos()
    {
        // si el cargo es Gerente, devuelve un array incluyendo el coste de los productos, así los vendedores no tienen acceso a ese dato
        $cargoGerente = $this->request->getPost('cargoGerente');

        $productosModel = model('Productos_model');
        $marcasModel = model('Marcas_model');
        $categoriasModel = model('Categorias_model');


        // Array que contendrá los datos de la tabla 
        $arrayDatos = array();

        // en la primera posición defino la cabecera de la tabla
        $arrayDatos[0] = array(
            'ID',
            'NOMBRE',
            'MARCA',
            'CATEGORÍA',
            'PRECIO',
            'OPCIONES'
        );


        // objeto con todos los productos, así puedo guardar en el array la marca y la categoría
        $objetoTodosProductos = $productosModel->obtenerTodosProductos();

        // guardo los datos de cada producto en un array, y lo pusheo en el array final
        foreach ($objetoTodosProductos as $key => $filaTodosProductos) {
            $arrayFila = array();

            $arrayFila[0] = $filaTodosProductos->id;
            $arrayFila[1] = $filaTodosProductos->nombre;

            // a partir de los id_marca e id_categoria, obtengo los nombres
            $arrayFila[2] = $marcasModel->obtenerMarcaById($filaTodosProductos->id_marca);
            $arrayFila[3] = $categoriasModel->obtenerCategoriaById($filaTodosProductos->id_categoria);

            $arrayFila[4] = $filaTodosProductos->precio;

            $arrayFila[5] = 'OPCIONES';

            // hago el push
            array_push($arrayDatos, $arrayFila);

        }
        // devuelvo el array con los productos
        return json_encode($arrayDatos);
    }

    /**
     * Función que comprueba si hay suficiente stock de un producto para realizar la venta
     * @return string JSON que indica si hay suficiente stock o no de ese producto
     */
    public function comprobarStock()
    {
        // cargo el modelo de productos
        $productosModel = model('Productos_model');

        // recojo los datos pasados por POST
        $idProducto = $this->request->getPost('idProducto');
        $cantidadProducto = $this->request->getPost('cantidadProducto');

        // devuelvo el boolean
        return json_encode($productosModel->comprobarStock($idProducto, $cantidadProducto));
    }

    /**
     * Función que resta el stock del producto vendido, inserta un registro en la tabla de pedidos, y tantos registros como productos vendidos en la tabla detalles de pedido
     * @return string JSON que indica el estado de la operación realizada y el mensaje que se debe mostrar en la alerta
     */
    public function procesarVenta()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // id del empleado que realizó la venta, para almacenarlo en la BD
        $idEmpleado = $session->get('idUser');

        // obtengo los datos pasados por POST
        $arrayProductosVenta = $this->request->getPost('arrayProductosVenta');
        $precioTotal = $this->request->getPost('precioTotal');

        // bandera que indica si algo ha fallado
        $banderaRegistrosInsertados = true;

        // cargo los modelos que necesitaré
        $productosModel = model('Productos_model');
        $pedidosModel = model('Pedidos_model');
        $detallesPedidoModel = model('DetallesPedido_model');

        // RESTO STOCK
        // recorro el array de los productos vendidos
        foreach ($arrayProductosVenta as $key => $value) {
            $idProducto = $value[0];
            $cantidadProducto = $value[1];

            // resto el stock
            $productosModel->restarStock($idProducto, $cantidadProducto);
        }

        // inserto los datos del pedido en la tabla pedidos
        // este método devuelve el id del registro insertado. Si devuelve 0, ha habido un problema
        $idPedido = $pedidosModel->insertarPedido($idEmpleado, $precioTotal);
        if ($idPedido == 0) {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Ha habido un problema al procesar el pedido'
                )
            );
        } else {
            // por cada producto, inserto en la tabla detalles de pedido
            foreach ($arrayProductosVenta as $key => $value) {
                $idProducto = $value[0];
                $cantidadProducto = $value[1];

                if ($detallesPedidoModel->insertarDetallesPedido($idPedido, $idProducto, $cantidadProducto)) {
                    $banderaRegistrosInsertados = true;
                } else {
                    $banderaRegistrosInsertados = false;
                }
                ;
            }

            if ($banderaRegistrosInsertados) {
                return json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Se ha procesado la venta con éxito'
                    )
                );
            } else {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Ha habido un error al procesar la venta'
                    )
                );
            }
        }

    }


}



