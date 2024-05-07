<?php

namespace App\Controllers;

// importo las clases necesarias de la biblioteca PhpSpreadsheet para manejar archivos de hojas de cálculo
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;

// controlador con funciones que varios controladores necesitan
class FuncionesGenerales extends BaseController
{
    /**
     * Método que genera un excel a partir de los datos de una tabla
     */
    function generarExcel()
    {
        // obtengo los datos pasados por POST
        $datosTabla = json_decode($this->request->getPost('datosTabla'));
        $archivo = $this->request->getPost('archivo');
        $ultimaColumna = $this->request->getPost('ultimaColumna');

        // creo una nueva spreadsheet
        $spreadsheet = new Spreadsheet();

        // guardo en $sheet la hoja activa (en este caso solo es 1)
        $sheet = $spreadsheet->getActiveSheet();

        // columna y fila donde comienza la tabla en el excel
        $col = 2;
        $fila = 5;

        // dejo las filas con autodimensionamiento
        foreach ($sheet->getRowDimensions() as $rowID) {
            $rowID->setRowHeight(-1);
        }

        // meto más anchura a las celdas
        for ($i = 3; $i <= 10; $i++) {
            $sheet->getColumnDimensionByColumn($i)->setWidth(17);
        }

        // Recorro el array pasado por POST para definir estilos para la cabecera
        foreach ($datosTabla as $key => $tabla) {
            // la posición 0 está vacía
            unset($tabla[0]);

            // estilos para el título del excel
            if (count($tabla) != '1') {
                // hago un merge de celdas
                $sheet->mergeCells('C4:' . $ultimaColumna . '4');
                // coloco el título
                $sheet->setCellValue('C4', $archivo);
                // meto fuente en negrita y cambio el tamaño
                $sheet->getStyle('C4')->getFont()->setBold(true);
                $sheet->getStyle('C4')->getFont()->setSize(16);

                // meto tamaño a las celdas del título
                $sheet->getStyle("C" . ($fila + 1) . ":" . $ultimaColumna . ($fila + 1))->getFont()->setSize(11); /*setSize para poner el tamaño de los elementos*/
                ;
            }

            // recorro el array de datos de la tabla pasado por POST
            foreach ($tabla as $arrRespuesta) {
                $fila++;
                // recorro cada fila
                foreach ($arrRespuesta as $datos) {
                    $col++;

                    //añado los valores a las celdas
                    if (count($tabla) != '1') {
                        $sheet->setCellValue([$col, $fila], $datos);
                    }

                    // meto bordes a todas las celdas
                    $sheet->getStyle([$col, $fila])->getBorders()->getAllBorders()->applyFromArray(['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]);

                    // meto color negro a la fila de las cabeceras
                    if ($fila == 6) {
                        $sheet->getStyle([$col, $fila])->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
                        $sheet->getStyle([$col, $fila])->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                        $sheet->getStyle([$col, $fila])->getFont()->setBold(true);
                    }
                }

                // centro los valores
                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];

                // aplico el styleArray a todas las celdas
                $range = 'C1:' . $ultimaColumna . $sheet->getHighestRow();
                $sheet->getStyle($range)->applyFromArray($styleArray);

                // dejo la columna = 2 para volver sacar la siguiente fila
                $col = 2;
            }

        }
        // creo el writer
        $writer = new Xlsx($spreadsheet);

        // guardo el excel generado en public/assets/excel
        $writer->save('./assets/excel/' . $archivo . '.xlsx');

        // devuelvo la ruta para redirigir y descargar el excel con js
        return json_encode('./assets/excel/' . $archivo . '.xlsx');
    }


    /**
     * Método que comprueba el cargo de un usuario y devuelve true si es gerente
     * @return string JSON que contiene el estado de la operación
     */
    public function comprobarCargoGerente()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        if ($session->get('cargo') == 'Gerente') {
            return json_encode(true);
        } else {
            return json_encode(false);
        }
    }

    /**
     * Método que cierra la sesión de un usuario
     * @return bool JSON que contiene el estado de la operación
     */
    public function cerrarSesion()
    {
        // inicializo el servicio de session
        $session = \Config\Services::session();

        // destruyo la sesión
        $session->destroy();

        return json_encode(true);
    }
}