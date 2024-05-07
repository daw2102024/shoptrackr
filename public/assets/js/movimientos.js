/**
 * Función que recoge los datos de una tabla (quitándo los de la última columna, que son de opciones)
 * @param {string} idTabla ID de la tabla de la cual obtendré los datos
 * @returns {Array} array de datos de la tabla
 */
function obtenerArrayTabla(idTabla) {
    // inicializo variables
    var arrayDeDatos = [];
    var contador = 0;
    var tablas = document.querySelectorAll(idTabla);

    // recorro cada tabla
    tablas.forEach(function (tabla) {
        // inicializo variables locales
        var datos = [];
        var tmp = 1;

        // recorro filas de encabezado
        var filasEncabezado = tabla.querySelectorAll('thead tr');
        filasEncabezado.forEach(function (fila) {
            var attr = fila.getAttribute('style');
            // verifico si la fila no está oculta
            if (!attr || attr === "") {
                datos[tmp] = [];
                // recorro celdas de encabezado
                var celdasEncabezado = fila.querySelectorAll('th:not(:last-child)');
                celdasEncabezado.forEach(function (celda, index) {
                    if (celda.textContent.trim() !== 'OPCIONES') {
                        datos[tmp][index] = celda.textContent.trim();
                    }
                });
                tmp++;
            }
        });

        // recorro filas de cuerpo
        var filasCuerpo = tabla.querySelectorAll('tbody tr');
        filasCuerpo.forEach(function (fila) {
            var attr = fila.getAttribute('style');
            // verifico si la fila no está oculta
            if (!attr || attr === "") {
                datos[tmp] = [];
                // recorro celdas de cuerpo
                var celdasCuerpo = fila.querySelectorAll('td:not(:last-child)');
                celdasCuerpo.forEach(function (celda, index) {
                    if (celda.textContent.trim() !== '') {
                        datos[tmp][index] = celda.textContent.trim();
                    } else {
                        var input = celda.querySelector('input');
                        if (input && input.value) {
                            datos[tmp][index] = input.value;
                        }
                    }
                });
                tmp++;
            }
        });

        // ajusto la cantidad de cabeceras
        var cabeceras = datos[1].length - (datos[datos.length - 1].length - 1);
        for (var i = 0; i < cabeceras - 1; i++) {
            datos[datos.length - 2].unshift("");
        }

        // guardo los datos en el array principal
        arrayDeDatos[contador] = datos;

        contador++;
    });

    return arrayDeDatos;
}

/**
 * Función que permite aplicar el dataTable a un tabla
 * @param {string} id Id de la tabla a la que se le quiere aplicar el dataTable
 * @return {Object} Objeto dataTable aplicado a esa tabla
 */
function crearDataTable(id) {
    const dataTable = $(id).DataTable({
        // Cambia el valor del filtrado por número de resultados
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],

        // Cambia el texto mostrado por cada opción
        language: {
            "search": "Buscar:",
            "lengthMenu": "Mostrando _MENU_ movimientos por página",
            "zeroRecords": "Ningún movimiento coincide con esas credenciales",
            "info": "Mostrando página <b>_PAGE_</b> de _PAGES_",
            "infoEmpty": "No hay movimientos disponibles",
            "infoFiltered": "(filtrado de <b>_MAX_</b> movimientos totales)"
        }
    });
    return dataTable;
}

/**
* Función que crea la tabla de movimientos y aplica dataTable
*/
function cargarTablaMovimientos() {

    // petición asíncrona que obtiene todos los pedidos de la base de datos
    $.ajax({
        type: "POST",
        url: "Movimientos/obtenerArrayMovimientos",
        dataType: "JSON",
        success: function (arrayMovimientos) {
            console.log(arrayMovimientos)

            let containerTablaMovimientos = document.querySelector('#containerTablaMovimientos');

            // creo la tabla
            const tablaMovimientos = document.createElement('table');
            tablaMovimientos.id = 'tablaMovimientos';
            tablaMovimientos.className = 'display';
            tablaMovimientos.style.width = '100%';

            // creo thead, tbody y tfoot
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');
            const tfoot = document.createElement('tfoot');

            // creo el botón de exportar a excel
            const btnExportar = document.createElement('button');
            btnExportar.innerHTML = 'Exportar movimientos a excel <i class="bi bi-file-earmark-spreadsheet-fill"></i>';
            btnExportar.id = 'btnExportar';

            // añado el evento de exportar a excel
            btnExportar.addEventListener('click', function () {
                exportarExcel(dataTableMovimientos, '#tablaMovimientos', 'Tabla de movimientos');
            });

            containerTablaMovimientos.appendChild(btnExportar);

            // los añado a la tabla
            tablaMovimientos.appendChild(thead);
            tablaMovimientos.appendChild(tbody);
            tablaMovimientos.appendChild(tfoot);

            // recorro todos los movimientos y creo las celdas
            for (let i = 0; i < arrayMovimientos.length; i++) {
                // cabeceras de la tabla, las meto en thead
                if (i == 0) {
                    crearCelda(arrayMovimientos[i], 'thead', thead);
                }
                else {
                    crearCelda(arrayMovimientos[i], 'tbody', tbody);
                }
            }

            // muestro los contenedores que había escondido, ya que la tabla ya está cargarda
            containerTablaMovimientos.appendChild(tablaMovimientos);
            containerTablaMovimientos.style.display = 'block';

            // muestro el container del boton de volver
            let containerBtnVolver = document.querySelector('#containerBtnVolver');
            containerBtnVolver.style.display = 'block';

            // aplico dataTable a la tabla de productos
            let dataTableMovimientos = crearDataTable('#tablaMovimientos');

        },
        error: function (error) {
            console.log(error);
        }
    });
}

/**
 * Función que crea las celdas (td) de la tabla de productos
 * @param {Array} filaTabla Datos de un producto
 * @param {string} tipo Indica a qué parte de la tabla pertenecerá la celda (thead, tbody o tfoot)
 * @param {Object} parte Parte de la tabla a la que pertenece la celda
 */
function crearCelda(filaTabla, tipo, parte) {
    const tr = document.createElement('tr');

    // Por cada producto obtenido, creo las celdas
    filaTabla.forEach(info => {

        let celda;
        // en función de a qué parte de la tabla pertenece la celda, se crea de una manera o de otra
        switch (tipo) {

            case 'thead':
                // al ser thead, creo th
                celda = document.createElement('th');
                celda.textContent = info;

                // cambio el id en el th de Opciones 
                if (info == 'OPCIONES') {
                    celda.id = 'thOpciones';
                }

                tr.appendChild(celda);
                break;

            case 'tbody':
                // al ser tbody, creo td
                celda = document.createElement('td');

                // en la columa de OPCIONES, creo los botones de editar y borrar en lugar de añadir el texto solamente
                if (info != 'OPCIONES') {
                    celda.textContent = info;
                }

                else if (info == 'OPCIONES') {

                    celda.id = 'tdOpciones';

                    // creo el botón de ver detalles y le doy formato
                    const btnVerDetalles = document.createElement('button');
                    btnVerDetalles.className = 'btnVerDetalles';
                    btnVerDetalles.innerHTML = '<i class="bi bi-eye-fill"></i>';



                    // añado el evento de ver detalles de pedido
                    btnVerDetalles.addEventListener('click', function () {
                        // guardo el id del pedido del que se quieren mostrar los detalles
                        const idPedido = btnVerDetalles.parentNode.parentNode.querySelectorAll('td')[0].innerText;
                        verDetallesPedido(idPedido);
                    });



                    // los añado a la celda
                    celda.appendChild(btnVerDetalles);
                }
                tr.appendChild(celda);

                break;
        }
    });
    parte.appendChild(tr);
}

/**
 * Función que crea una tabla con los detalles del pedido clickado
 * @param {int} idPedido id del pedido clickado

 */
function verDetallesPedido(idPedido) {

    // creo el contenedor de la tabla de detalles
    const containerTablaDetalles = document.createElement('div');
    containerTablaDetalles.className = 'container';
    containerTablaDetalles.id = 'containerTablaDetalles';

    // creo la tabla
    const tablaDetalles = document.createElement('table');
    tablaDetalles.id = 'tablaDetalles';
    containerTablaDetalles.appendChild(tablaDetalles);


    // creo el thead y el tboby
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');

    // creo un th con el título y lo añado al thead
    const trPrincipal = document.createElement('tr');
    const thPrincipal = document.createElement('th');
    thPrincipal.colSpan = 4;
    thPrincipal.id = 'thPrincipal';
    thPrincipal.innerHTML = 'DETALLES DEL PEDIDO N° ' + idPedido + ' <i class="bi bi-info-circle-fill"></i>';
    trPrincipal.appendChild(thPrincipal);
    thead.appendChild(trPrincipal);

    const tr = document.createElement('tr');
    // cabeceras que tendrá la tabla
    const cabecarasTablaDetalles = [
        'ID DETALLE',
        'PRODUCTO',
        'MARCA',
        'CANTIDAD'
    ]

    // creo los th de la cabecera
    cabecarasTablaDetalles.forEach(cabecera => {
        const th = document.createElement('th');
        th.innerText = cabecera;
        tr.appendChild(th);
    })
    thead.appendChild(tr);

    // petición asíncrona que obtiene los detalles del pedido pasado por POST
    $.ajax({
        type: "POST",
        url: "Movimientos/obtenerDetallesPedido",
        // paso el idPedido
        data: { idPedido: idPedido },
        dataType: "JSON",
        success: function (arrayDetallesPedido) {

            // si hay contenedor de tablaDetalles, lo borro para volverlo a cargar, lo hago ahora para que no haya delay al hacer la petición
            if (document.querySelector('#containerTablaDetalles')) {
                document.querySelector('#containerTablaDetalles').remove();
            }

            // por cada fila de detalles (cada producto comprado)
            arrayDetallesPedido.forEach(fila => {
                // creo el tr y creo los tds con cada detalle
                const tr = document.createElement('tr');

                fila.forEach(detalle => {
                    const td = document.createElement('td');
                    td.innerText = detalle;
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });

            // añado el container, el thead y el tbody ahora que están cargados los datos
            tablaDetalles.appendChild(thead);
            tablaDetalles.appendChild(tbody);
            document.body.appendChild(containerTablaDetalles);

            // vuelvo a añadir el container de volver para que salga debajo del container de la tabla de detalles
            document.body.appendChild(document.querySelector('#containerBtnVolver'));

        },
        error: function (error) {
            console.log(error);
        }
    });
}

/**
 * Función que hace una petición asíncrona para generar un excel y lo descarga
 * @param {Object} dataTable dataTable de la tabla de la que se quiere generar el excel
 * @param {string} idTabla id de la tabla de la que se quiere generar el excel
 * @param {string} archivo nombre y título que tendrá el excel
 */
function exportarExcel(dataTable, idTabla, archivo) {
    // destruyo el dataTable para obtener todos los valores de las filas
    dataTable.destroy();

    // obtengo el array de datos de todas las filas
    let datosTabla = obtenerArrayTabla(idTabla);

    // vuelvo a crear el dataTable
    dataTableProductos = crearDataTable(idTabla);

    // petición asíncrona que genera el excel y devuelve la ruta para descargarlo
    $.ajax({
        type: "POST",
        url: "FuncionesGenerales/generarExcel",
        // le paso la ultimaColumna = J ya que el excel empezará en la C y son 8 columnas
        data: { 'datosTabla': JSON.stringify(datosTabla), archivo: archivo, ultimaColumna: 'F' },
        dataType: "JSON",
        success: function (response) {
            // abro la ruta del excel para descargarlo
            window.location.href = response;
        },
        error: function (error) {
            console.log(error);
        }
    });
}

const containerTablaMovimientos = document.querySelector('#containerTablaMovimientos');
containerTablaMovimientos.style.display = 'none';

const containerBtnVolver = document.querySelector('#containerBtnVolver');
containerBtnVolver.style.display = 'none';

// inicializo la variable dataTable, donde almacenaré el objeto de dataTable tras cargar la tabla
let dataTableMovimientos = '';

cargarTablaMovimientos();


