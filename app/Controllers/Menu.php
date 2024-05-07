<?php

namespace App\Controllers;

// controlador correspondiente al menú principal
class Menu extends BaseController
{
    public function index()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // si está la sesión creada, saco la vista del menu
        if ($session->get("user")) {
            return view("menu");

        } else {
            // si no está la sesión creada, redirijo al login
            return redirect()->to('/login');
        }
    }

    /**
     * Función que comprueba el stock de todos los productos para mostrar notificaciones en la vista
     * @return string JSON que contiene el array con los productos en "peligro" de stock
     */
    public function comprobarStocks()
    {
        // cargo el modelo de productos
        $productosModel = model('Productos_model');

        // array con todos los productos cerca del stock mínimo
        $productosCercaStockMinimo = $productosModel->obtenerProductosByStock('CercaStockMinimo');

        // array con todos los productos por debajo del stock mínimo
        $productosDebajoStockMinimo = $productosModel->obtenerProductosByStock('DebajoStockMinimo');

        // array con todos los productos sin stock
        $productosSinStock = $productosModel->obtenerProductosByStock('SinStock');

        // devuelvo un array que contiene lo 3 arrays anteriores
        $comprobacionStocks = array(
            $productosCercaStockMinimo,
            $productosDebajoStockMinimo,
            $productosSinStock
        );

        return json_encode($comprobacionStocks);
    }
}
