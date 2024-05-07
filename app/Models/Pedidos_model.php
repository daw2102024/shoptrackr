<?php

namespace App\Models;

use CodeIgniter\Model;

// controlador correspondiente al a tabla "pedidos"
class Pedidos_model extends Model
{
    // defino el nombre de la tabla
    protected $table = 'pedidos';

    /**
     * Función que devuelve un array con todos los pedidos cargados en la base de datos
     * @return array con todos los pedidos cargados en la base de datos
     */
    public function obtenerTodosPedidos()
    {
        // cargo el builder de la tabla de pedidos
        $builder = $this->db->table($this->table);

        $builder->select('*');

        // devuelvo el resultado de la query
        return $builder->get()->getResult();
    }


    /**
     * Función que inserta un nuevo registro en la tabla pedidos y devuelve el id correspondiente a ese registro
     * @param int $idEmpleado id del vendedor que realizó la venta
     * @param float $precioTotal precio total de la venta
     * @return int int que indica cuál es el id del registro insertado
     */
    public function insertarPedido($idEmpleado, $precioTotal)
    {
        // cargo el builder de la tabla de pedidos
        $builder = $this->db->table($this->table);

        $data = array(
            'id_vendedor' => $idEmpleado,
            'precio_total' => $precioTotal,
            'hora' => date('Y-m-d H:i:s') // Hora actual (formato 'YYYY-MM-DD HH:MM:SS')
        );

        // si se interta con éxito, devuelve  el id del registro insertado
        if ($builder->insert($data)) {
            return $this->db->insertID();
        } else {
            return 0;
        }
    }

}
