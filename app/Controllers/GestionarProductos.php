<?php

namespace App\Controllers;

// importo la clase IOFactory de la biblioteca PhpSpreadsheet para manejar archivos de hojas de cálculo
use PhpOffice\PhpSpreadsheet\IOFactory;

// controlador correspondiente a la gestión de productos
class GestionarProductos extends BaseController
{
    public function index()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // si está la sesión creada, saco la vista del menu
        if ($session->get("user")) {
            return view("gestionarProductos");

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

        // al haber pasado el boolean por Post, pasa a ser string
        if ($cargoGerente == 'true') {
            $arrayDatos[0] = array(
                'ID',
                'NOMBRE',
                'MARCA',
                'CATEGORÍA',
                'PRECIO',
                'COSTE',
                'STOCK',
                'STOCK_MÍNIMO',
                'OPCIONES'
            );
        } else {
            $arrayDatos[0] = array(
                'ID',
                'NOMBRE',
                'MARCA',
                'CATEGORÍA',
                'PRECIO',
                'STOCK',
            );
        }

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

            // dependiendo de si es Gerente o no, paso unos datos u otros
            if ($cargoGerente == 'true') {
                $arrayFila[5] = $filaTodosProductos->coste;
                $arrayFila[6] = $filaTodosProductos->stock;
                $arrayFila[7] = $filaTodosProductos->stock_minimo;
                $arrayFila[8] = 'OPCIONES';
            } else {
                $arrayFila[5] = $filaTodosProductos->stock;
            }

            // hago el push
            array_push($arrayDatos, $arrayFila);

        }
        // devuelvo el array con los productos
        return json_encode($arrayDatos);
    }


    /**
     * Función que devuelve un array con todas las marcas de la base de datos
     * @return string JSON que contiene el array con esos datos
     */
    public function obtenerArrayMarcas()
    {
        // cargo el modelo de marcas
        $marcasModel = model('Marcas_model');

        // devuelvo un array con todas las marcas
        return json_encode($marcasModel->obtenerTodasMarcas());
    }

    /**
     * Función que devuelve un array con todas las categorías de la base de datos
     * @return string JSON que contiene el array con esos datos
     */
    public function obtenerArrayCategorias()
    {
        // cargo el modelo de categorías
        $categoriasModel = model('Categorias_model');

        // devuelvo un array con todas las categorías
        return json_encode($categoriasModel->obtenerTodasCategorias());
    }

    /**
     * Función que inserta un nuevo producto en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function insertarProductoBD()
    {
        // cargo el modelo de productos
        $productosModel = model('Productos_model');

        // obtengo el array con los datos que se quieren insertar por POST
        $datosFila = $this->request->getPost('datosFila');

        // creo un array con los datos del producto que se va a insertar
        $datosProducto = array();

        $datosProducto['id'] = $datosFila[0];
        $datosProducto['nombre'] = $datosFila[1];
        $datosProducto['id_marca'] = $datosFila[2];
        $datosProducto['id_categoria'] = $datosFila[3];
        $datosProducto['precio'] = $datosFila[4];
        $datosProducto['coste'] = $datosFila[5];
        $datosProducto['stock'] = $datosFila[6];
        $datosProducto['stock_minimo'] = $datosFila[7];

        // Compruebo si ese id ya existe en la BD, y si es así, devuelvo el error
        if ($this->comprobarSiExisteProducto($datosProducto['id'])) {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Ya existe un producto con ese <b>id</b> en la Base de Datos'
                )
            );

        } else if ($productosModel->comprobarProductoByNombre($datosProducto['nombre'])) {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Ya existe un producto con ese <b>nombre</b> en la Base de Datos'
                )
            );
        } else {
            // todas las comprobaciones realizadas, hago la inserción en la base de datos
            // no me hace falta comprobar si existen esa marca y esa categoría ya que se inserta a partir de select option obtenidos directamente de la bd

            // Si devuelve true, la inserción se ha realizado con éxito, de lo contrario ha habido un error
            if ($productosModel->insertarProducto($datosProducto)) {
                return json_encode(
                    array(
                        'status' => 'success',
                        'message' => 'Se ha insertado <b>' . $datosProducto['nombre'] . '</b> en la Base de Datos con éxito.'
                    )
                );
            } else {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'Ha habido un error insertando el producto en la base de datos'
                    )
                );
            }
        }
    }

    /**
     * Función que borra un producto de la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function borrarProductoBD()
    {
        // cargo el modelo de productos
        $productosModel = model('Productos_model');

        // obtengo el id y el nombre del producto pasados por POST
        $idProducto = $this->request->getPost('idProducto');
        $nombreProducto = $this->request->getPost('nombreProducto');

        // si devuelve true, se ha borrado el producto con éxito, devuelvo el status y el mensaje correspondientes
        if ($productosModel->borrarProducto($idProducto)) {
            return json_encode(
                array(
                    'status' => 'success',
                    'message' => 'Se ha borrado el producto <b>' . $nombreProducto . '</b> de la Base de Datos con éxito.'
                )
            );
        } else {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Ha habido un error borrando el producto de la base de datos'
                )
            );
        }
    }

    /**
     * Función que edita un producto de la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function editarProductoBD()
    {
        // cargo el modelo de productos
        $productosModel = model('Productos_model');

        // obtengo el array con los datos del producto que se quiere editar
        $datosEditados = $this->request->getPost('datosEditados');

        // creo un array con los datos del producto que se va a editar
        $datosProducto['id'] = $datosEditados[0];
        $datosProducto['nombre'] = $datosEditados[1];
        $datosProducto['id_marca'] = $datosEditados[2];
        $datosProducto['id_categoria'] = $datosEditados[3];
        $datosProducto['precio'] = $datosEditados[4];
        $datosProducto['coste'] = $datosEditados[5];
        $datosProducto['stock'] = $datosEditados[6];
        $datosProducto['stock_minimo'] = $datosEditados[7];

        // no necesito realizar comprobaciones, ya que el ID no se puede editar y la marca y categoría se seleccionan con un select option cargado directamente de la BD
        if ($productosModel->editarProducto($datosProducto)) {
            return json_encode(
                array(
                    'status' => 'success',
                    'message' => 'Se ha editado el producto <b>' . $datosProducto['nombre'] . '</b> en la Base de Datos con éxito.'
                )
            );
        } else {
            return json_encode(
                array(
                    'status' => 'error',
                    'message' => 'Ha habido un error editando el producto en la base de datos'
                )
            );
        }
    }

    /**
     * Función que comprueba si hay alguna marca con ese id y la inserta en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function insertarMarcaCategoriaBD()
    {
        // obtengo los datos pasados por POST
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');

        // indica si tengo que insertar a marcas o a categorias
        $tabla = $this->request->getPost('tabla');

        switch ($tabla) {
            case 'marca':
                // cargo el modelo de marcas
                $marcasModel = model('Marcas_model');

                // Compruebo si ese id ya existe en la BD, y si es así, devuelvo el error
                if ($marcasModel->comprobarSiExisteMarca($id)) {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ya existe una marca con ese <b>id</b> en la Base de Datos'
                        )
                    );
                }
                // compruebo si ya existe una marca con ese nombre
                else if ($marcasModel->comprobarSiExisteMarcaByNombre($nombre)) {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ya existe una marca con ese <b>nombre</b> en la Base de Datos'
                        )
                    );
                } else {
                    // inserto en la base de datos y devuelvo un mensaje informativo
                    if ($marcasModel->insertarMarca($id, $nombre)) {
                        return json_encode(
                            array(
                                'status' => 'success',
                                'message' => 'Se ha insertado <b>' . $nombre . '</b> en la Base de Datos con éxito.'
                            )
                        );
                    } else {
                        return json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Ha habido un error insertando la marca en la base de datos'
                            )
                        );
                    }
                }
            case 'categoria':
                // cargo el modelo de marcas
                $categoriasModel = model('Categorias_model');

                // Compruebo si ese id ya existe en la BD, y si es así, devuelvo el error
                if ($categoriasModel->comprobarSiExisteCategoria($id)) {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ya existe una categoría con ese <b>id</b> en la Base de Datos'
                        )
                    );
                }
                // compruebo si ya existe una categoria con ese nombre
                else if ($categoriasModel->comprobarSiExisteCategoriaByNombre($nombre)) {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ya existe una marca con ese <b>nombre</b> en la Base de Datos'
                        )
                    );
                } else {
                    // inserto en la base de datos y devuelvo un mensaje informativo
                    if ($categoriasModel->insertarCategoria($id, $nombre)) {
                        return json_encode(
                            array(
                                'status' => 'success',
                                'message' => 'Se ha insertado <b>' . $nombre . '</b> en la Base de Datos con éxito.'
                            )
                        );
                    } else {
                        return json_encode(
                            array(
                                'status' => 'error',
                                'message' => 'Ha habido un error insertando la categoría en la base de datos'
                            )
                        );
                    }
                }
        }
        // en el caso de que no sea marca o categoria (que no debería pasar), devuelvo null
        return null;

    }

    /**
     * Función que comprueba si hay algún producto de esa marca y lo borra de la BD
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function borrarMarcaCategoriaBD()
    {
        // indica qué modelo cargar
        $tabla = $this->request->getPost('tabla');

        // obtengo los datos pasados por POST
        $id = $this->request->getPost('id');
        $nombre = $this->request->getPost('nombre');

        // cargo el modelo de productos
        $productosModel = model('Productos_model');



        if ($tabla == 'marca') {
            $marcasModel = model('Marcas_model');
            // compruebo si hay algún producto con esa marca, si es así, devuelvo un mensaje informando
            if ($productosModel->comprobarProductosConMarca($id)) {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'No puedes borrar esta marca. Hay productos de esa marca en la BD'
                    )
                );
            }
            // borro la marca y notifico el estado
            else {
                if ($marcasModel->borrarMarca($id)) {
                    return json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Se ha borrado la marca <b>' . $nombre . '</b> de la Base de Datos con éxito.'
                        )
                    );
                } else {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ha habido un error borrando la marca de la base de datos'
                        )
                    );
                }
            }
        } else {
            $categoriasModel = model('Categorias_model');
            // compruebo si hay algún producto con esa categoría, si es así, devuelvo un mensaje informando
            if ($productosModel->comprobarProductosConCategoria($id)) {
                return json_encode(
                    array(
                        'status' => 'error',
                        'message' => 'No puedes borrar esta categoría. Hay productos de esa categoría en la BD'
                    )
                );
            }
            // borro la marca y notifico el estado
            else {
                if ($categoriasModel->borrarCategoria($id)) {
                    return json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Se ha borrado la categoría <b>' . $nombre . '</b> de la Base de Datos con éxito.'
                        )
                    );
                } else {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ha habido un error borrando la categoría de la base de datos'
                        )
                    );
                }
            }
        }
    }

    /**
     * Función que edita una marca o categoria en la base de datos
     * @return string JSON que contiene el estado de la operación y un mensaje informativo
     */
    public function editarMarcaCategoriaBD()
    {
        // indica a qué modelo llamar
        $tabla = $this->request->getPost('tabla');

        // obtengo los datos pasados por POST
        $id = $this->request->getPost('idFilaEditada');
        $nuevoNombre = $this->request->getPost('nuevoNombre');

        switch ($tabla) {
            case 'marca':
                $marcasModel = model('Marcas_model');

                // comprueba si ya existe una marca con ese nombre
                if ($marcasModel->comprobarSiExisteMarcaByNombre($nuevoNombre)) {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'No se puede editar la marca, <b>ese nombre ya está en uso</b>.'
                        )
                    );
                }

                // todas las validaciones hechas, edito la marca y devuelvo el mensaje correspondiente
                if ($marcasModel->editarMarca($id, $nuevoNombre)) {
                    return json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Se ha editado la marca <b>' . $nuevoNombre . '</b> en la Base de Datos con éxito.'
                        )
                    );
                } else {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ha habido un error editando la marca en la base de datos'
                        )
                    );
                }
            case 'categoria':
                $categoriasModel = model('Categorias_model');

                // comprueba si ya existe una categoria con ese nombre
                if ($categoriasModel->comprobarSiExisteCategoriaByNombre($nuevoNombre)) {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'No se puede editar la categoría, <b>ese nombre ya está en uso</b>.'
                        )
                    );
                }

                // todas las validaciones hechas, edito la categoría y devuelvo el mensaje correspondiente
                if ($categoriasModel->editarCategoria($id, $nuevoNombre)) {
                    return json_encode(
                        array(
                            'status' => 'success',
                            'message' => 'Se ha editado la categoria <b>' . $nuevoNombre . '</b> en la Base de Datos con éxito.'
                        )
                    );
                } else {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'message' => 'Ha habido un error editando la categoria en la base de datos'
                        )
                    );
                }
        }
        // en el caso de que no sea marca o categoria (que no debería pasar), devuelvo null
        return null;

    }

    /**
     * Función que comprueba si existe un producto con ese id en la base de datos
     * @return boolean Boolean que indica si ese producto ya existe en la base de datos
     */
    public function comprobarSiExisteProducto($idProducto)
    {
        // cargo el modelo de productos
        $productosModel = model('Productos_model');

        // devuelvo el boolean
        return $productosModel->obtenerProductoById($idProducto);
    }

    /**
     * Función que comprueba si existe un producto con ese id en la base de datos
     * @return boolean Boolean que indica si el stock se ha actualizado correctamente
     */
    public function cargarStock()
    {
        // obtengo el excel enviado
        $excelCargado = $_FILES['excel'];

        // por alguna razón, la función que da Codeigniter 4 para leer ficheros no funciona
        // $excel = $this->request->getFile('excel');

        // cargo el modelo de productos, que es el que necesitaré para insertar stock y hacer validaciones
        $productosModel = model('Productos_model');

        // ruta en la que guardaré el excel cargado para leerlo posteriormente
        $rutaExcel = './assets/excel/import/';

        // pillo el nombre y lo añado a la ruta
        $nombreExcel = $excelCargado['name'];
        $nombreFicheroPartido = explode('\\', $nombreExcel);
        $rutaImport = $rutaExcel . end($nombreFicheroPartido);

        // muevo el excel cargado a la ruta del codeigniter
        move_uploaded_file($excelCargado['tmp_name'], $rutaImport);

        // creo el objeto con el excel cargado, ahora ya puedo leerlo
        $excel = IOFactory::load($rutaImport);
        $sheet = $excel->getActiveSheet();

        // datos que se devolverán al js
        $datosLeidos = array();

        // recorro cada fila
        foreach ($sheet->getRowIterator() as $fila) {
            // datos de cada fila
            $datosFila = array();

            // bandera para saber si hay que hacer el push de la fila a los datosLeidos
            $filaVacia = true;

            // recorro cada celda de cada fila
            foreach ($fila->getCellIterator() as $celda) {

                // Comprobamos si el dato esta vacio, y, si lo esta, lo saltamos directamente
                if ($celda->getValue() != '') {
                    $filaVacia = false;

                    // Obtenemos el valor de la celda, quitando espacios por delante y detrás para evitar problemas indeseados
                    $valorCelda = trim($celda->getValue());
                    array_push($datosFila, $valorCelda);
                }
            }
            if (!$filaVacia) {
                array_push($datosLeidos, $datosFila);
            }
        }
        // banderas que indican si hay algún error
        $productoNoExiste = false;
        $stockActualizado = false;
        $stockIncorrecto = false;

        // recorro el array generado para actualizar el stock
        foreach ($datosLeidos as $key => $fila) {

            // compruebo que el stock es un número entero y positivo
            if ($fila[1] > 0) {
                // esto indicará si se quiere actualizar stock a productos por id o por nombre del producto
                if (is_numeric($fila[0])) {
                    // llamo al método que comprueba si existe un producto con esa id en la bd
                    if ($productosModel->obtenerProductoById($fila[0])) {

                        // obtengo el stock del producto
                        $stockActual = $productosModel->obtenerStockById($fila[0]);
                        // actualizo el stock de ese producto
                        if ($productosModel->sumarStockProducto('id', $fila[0], intval($stockActual), intval($fila[1]))) {
                            // bandera que indica que se han actualizado stocks, para sacar alertas
                            $stockActualizado = true;
                        }


                    } else {
                        // bandera que indica que hay algún producto que no se ha encontrado, para sacar alertas
                        $productoNoExiste = true;
                    }
                } else {

                    // llamo al método que comprueba si existe un producto con ese nombre en la bd
                    if ($productosModel->comprobarProductoByNombre($fila[0])) {
                        // obtengo el stock del producto
                        $stockActual = $productosModel->obtenerStockByNombre($fila[0]);
                        // actualizo el stock de ese producto
                        if ($productosModel->sumarStockProducto('nombre', $fila[0], intval($stockActual), intval($fila[1]))) {
                            // bandera que indica que se han actualizado stocks, para sacar alertas
                            $stockActualizado = true;
                        }

                    } else {
                        // bandera que indica que hay algún producto que no se ha encontrado, para sacar alertas
                        $productoNoExiste = true;
                    }
                }
            } else {
                // bandera que indica que el stock no es válido
                $stockIncorrecto = true;
            }
        }

        // array que contendrá la respuesta
        $response = array();
        $response['status'] = 'error';

        // mensajes indicativos para cada situación
        if ($stockIncorrecto && !$stockActualizado) {
            $response['message'] = '<b>No se ha podido actualizar el stock</b>. Uno o más valores de stock no son válidos (deben ser números enteros positivos) y ningún stock ha sido actualizado.';
        } else if ($stockIncorrecto && $stockActualizado) {
            $response['status'] = 'success';
            $response['message'] = '<b>Se han actualizado algunos stocks</b>, pero uno o más valores de stock no son válidos (deben ser números enteros positivos).';
        } else if ($productoNoExiste && !$stockActualizado) {
            $response['message'] = '<b>No se ha podido actualizar el stock</b>. Uno o más productos no existen en la base de datos y ningún stock ha sido actualizado.';
        } else if ($productoNoExiste && $stockActualizado) {
            $response['status'] = 'success';
            $response['message'] = '<b>Se han actualizado algunos stocks</b>, pero uno o más productos no existen en la base de datos.';
        } else if (!$stockActualizado) {
            $response['message'] = '<b>No se ha podido actualizar el stock</b>. No se han encontrado productos que coincidan con los datos introducidos.';
        } else {
            $response['status'] = 'success';
            $response['message'] = 'El <b>stock</b> ha sido <b>actualizado correctamente.</b>';
        }

        // devuelvo la respuesta del servidor
        return json_encode($response);

    }
}


