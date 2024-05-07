<?php

namespace App\Models;

use CodeIgniter\Model;

// controlador correspondiente al a tabla "productos"
class Productos_model extends Model
{
    // defino el nombre de la tabla
    protected $table = 'productos';

    /**
     * Función que devuelve un array con todos los productos cargados en la base de datos
     * @return array con todos los productos cargados en la base de datos
     */
    public function obtenerTodosProductos()
    {
        // cargo el builder de la tabla de productos
        $builder = $this->db->table($this->table);

        $builder->select('*');

        // devuelvo el resultado de la query
        return $builder->get()->getResult();
    }

    /**
     * Función que devuelve un boolean que indica si existe un producto con esa id en la base de datos
     * @param string $idProducto id del producto que se quiere comprobar si ya existe en la base de datos
     * @return boolean que indica si ese id ya existe
     */
    public function obtenerProductoById($idProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('*');
        $builder->where('id', $idProducto);

        // si existe un producto con ese id, devuelvo true para cancelar la inserción
        if (!empty($builder->get()->getResult())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que el nombre del producto cuya id coincide con la pasada por parámetro
     * @param string $idProducto id del producto del que se quiere obtener el nombre
     * @return string nombre del producto
     */
    public function obtenerNombreProductoById($idProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('nombre');
        $builder->where('id', $idProducto);

        return $builder->get()->getRow()->nombre;
    }

    /**
     * Función que la marca de un producto cuya id coincide con la pasada por parámetro
     * @param string $idProducto id del producto del que se quiere obtener la marca
     * @return string marca del producto
     */
    public function obtenerMarcaProductoById($idProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('id_marca');
        $builder->where('id', $idProducto);

        return $builder->get()->getRow()->id_marca;
    }

    /**
     * Función que inserta un nuevo producto en la base de datos
     * @param array $datosProducto datos del producto que se quiere insertar
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function insertarProducto($datosProducto)
    {
        // array con los datos del producto que se quiere insertar
        $data = [
            'id' => $datosProducto['id'],
            'nombre' => $datosProducto['nombre'],
            'id_marca' => $datosProducto['id_marca'],
            'id_categoria' => $datosProducto['id_categoria'],
            'precio' => $datosProducto['precio'],
            'coste' => $datosProducto['coste'],
            'stock' => $datosProducto['stock'],
            'stock_minimo' => $datosProducto['stock_minimo']
        ];

        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        // si se interta con éxito, devuelve  true, si no false
        if ($builder->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que borra un producto de la base de datos a partir de su id
     * @param string $idProducto id del producto que se quiere borrar
     * @return boolean que indica si ese id ya existe
     */
    public function borrarProducto($idProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        // borro el producto con ese id
        $builder->where('id', $idProducto);

        // si se borra con éxito, devuelvo true, si no false
        if ($builder->delete()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que inserta un nuevo producto en la base de datos
     * @param array $datosProducto datos del producto que se quiere editar
     * @return boolean que indica si la operación se realizó con éxito
     */
    public function editarProducto($datosProducto)
    {
        // array con los datos del producto que se quiere editar
        $data = array(
            'nombre' => $datosProducto['nombre'],
            'id_marca' => $datosProducto['id_marca'],
            'id_categoria' => $datosProducto['id_categoria'],
            'precio' => $datosProducto['precio'],
            'coste' => $datosProducto['coste'],
            'stock' => $datosProducto['stock'],
            'stock_minimo' => $datosProducto['stock_minimo']
        );

        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        // para el producto con ese id, hago el update con los datos recibidos
        $builder->where('id', $datosProducto['id']);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Función que devuelve los productos que pertenecen a un criterio
     * @param string $criterio indica el nivel de los productos que se quieren buscar (CercaStockMinimo, DebajoStockMinimo, SinStock)
     * @return array array con los productos que pertenecen al criterio indicado
     */
    public function obtenerProductosByStock($criterio)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        // selecciono losamente el id y el nombre, que es lo que necesito
        $builder->select('id, nombre');

        // en función del criterio, el where es diferente
        switch ($criterio) {
            case 'CercaStockMinimo':
                // el stock está 5 unidades por encima del stock mínimo
                $builder->where('stock <= stock_minimo + 5');
                $builder->where('(stock >= stock_minimo AND stock > 0)', null, false); // condición adicional que evita que salgan productos por debajo del stock minimo / sin stock
                break;

            case 'DebajoStockMinimo':
                // el stock está por encima del stock mínimo
                $builder->where('stock < stock_minimo');
                $builder->where('stock > 0'); // condición adicional que evita que salgan productos sin stock
                break;

            case 'SinStock':
                // el stock está a 0
                $builder->where('stock', 0);
                break;
        }

        // devuelvo el resultado de la query
        return $builder->get()->getResultArray();
    }

    /**
     * Función que comprueba el stock de los productos vendidos, para procesar la venta o no
     * @param array $arrayProductosVenta array con el id y la cantidad de los productos vendidos
     * @return boolean que indica si hay stock suficiente para realizar la venta
     */
    public function comprobarStock($idProducto, $cantidadProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('nombre, stock');

        // busco el producto por su id
        $builder->where('id', $idProducto);

        // obtengo la fila
        $row = $builder->get()->getRow();

        // si el stock es menor a la cantidad comprada
        if ($row->stock < $cantidadProducto) {
            // devuelvo false
            return false;
        } else {
            // devuelvo true
            return true;
        }
    }

    /**
     * Función que resta la cantidad vendida al stock de un producto
     * @param int $idProducto id del producto del que hay que restar el stock
     * @param int $cantidadProducto cantidad que hay que restar al stock
     * @return boolean que indica si la operación se realizó con éxito o no
     */
    public function restarStock($idProducto, $cantidadProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('stock');
        $builder->where('id', $idProducto);

        // obtengo la fila
        $row = $builder->get()->getRow();


        $stockActualizado = $row->stock - $cantidadProducto;

        // array con los datos que se modificarán, en este caso solo el stock
        $data = array(
            'stock' => $stockActualizado
        );

        // para el producto con ese id, hago el update con el nuevo stock
        $builder->where('id', $idProducto);

        // si se actualiza con éxito, devuelve true, si no false
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * Función que comprueba si hay productos en la base de datos con ese id de marca
     * @param int $id id de la marca que se quiere comprobar
     * @return boolean que indicará si hay o no productos
     */
    public function comprobarProductosConMarca($id)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        // busco por ese id de marca
        $builder->where('id_marca', $id);

        // obtengo el número de resultados obtenidos
        $count = $builder->countAllResults();

        // devuelvo true si no se devuelven resultados
        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Función que comprueba si hay productos en la base de datos con ese id de categoria
     * @param int $id id de la categoria que se quiere comprobar
     * @return boolean que indicará si hay o no productos
     */
    public function comprobarProductosConCategoria($id)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        // busco por ese id de categoria
        $builder->where('id_categoria', $id);

        // obtengo el número de resultados obtenidos
        $count = $builder->countAllResults();

        // devuelvo true si no se devuelven resultados
        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Función que comprueba si hay productos en la base de datos con ese nombre
     * @param string $nombreProducto nombre del producto que se quiere comprobar
     * @return boolean que indicará si hay o no productos con ese nombre
     */
    public function comprobarProductoByNombre($nombreProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('*');
        $builder->where('nombre', $nombreProducto);

        // si existe un producto con ese id, devuelvo true para cancelar la inserción
        if (!empty($builder->get()->getResult())) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que devuelve el stock de un producto, buscando por su id
     * @param int $idProducto id del producto que se quiere obtener el stock
     * @return int stock del producto
     */
    public function obtenerStockById($idProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('stock');
        $builder->where('id', $idProducto);

        return $builder->get()->getRow()->stock;
    }

    /**
     * Función que devuelve el stock de un producto, buscando por su nombre
     * @param string $nombreProducto nombre del producto que se quiere obtener el stock
     * @return int stock del producto
     */
    public function obtenerStockByNombre($nombreProducto)
    {
        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->select('stock');
        $builder->where('nombre', $nombreProducto);

        return $builder->get()->getRow()->stock;
    }

    /**
     * Función que actualiza el stock de un producto
     * @param string $columna nombre de la columna por la que se quiere buscar el producto
     * @param string $producto nombre o id del producto que se quiere actualizar el stock
     * @param int $stockActual stock actual de ese producto
     * @param int $stockAnadido stock que se quiere sumar
     */
    public function sumarStockProducto($columna, $producto, $stockActual, $stockAnadido)
    {
        

        $data = array(
            'stock' => $stockActual + $stockAnadido
        );

        // cargo el builder de la tabla productos
        $builder = $this->db->table($this->table);

        $builder->where($columna, $producto);
        if ($builder->update($data)) {
            return true;
        } else {
            return false;
        }

    }
}

