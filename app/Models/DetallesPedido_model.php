<?php

namespace App\Models;

use CodeIgniter\Model;

// controlador correspondiente al a tabla "detalles_pedido"
class DetallesPedido_model extends Model
{
    // defino el nombre de la tabla
    protected $table = 'detalles_pedido';


    /**
     * Función que inserta un nuevo registro en la detalles de pedido
     * @param int $idPedido id del pedido al que hace referencia los detalles
     * @param float $idProducto id del producto que pertenece a ese pedido
     * @param float $cantidadProducto cantidad de ese producto comprado
     * @return bool que indica si se realizó la inserción con éxito o no
     */
    public function insertarDetallesPedido($idPedido, $idProducto, $cantidadProducto)
    {
        // cargo el builder de la tabla de detalles de pedido
        $builder = $this->db->table($this->table);

        $data = array(
            'id_pedido' => $idPedido,
            'id_producto' => $idProducto,
            'cantidad' => $cantidadProducto
        );

        // si se interta con éxito, devuelve  el id del registro insertado
        if ($builder->insert($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Función que devuelve todos los detalles de un pedido en función de la id pasada por parámetro
     * @param int $idPedido id del pedido al que hace referencia los detalles
     * @return array con todos los detalles de ese pedido
     */
    public function obtenerDetallesPedidoByIdPedido($idPedido)
    {
        // cargo el builder de la tabla de detalles de pedido
        $builder = $this->db->table($this->table);

        // selecciono todos los datos de los detalles que coinciden con idPedido
        $builder->select('*');
        $builder->where('id_pedido', $idPedido);

        // devuelvo el resultado
        return $builder->get()->getResult();
    }


}
