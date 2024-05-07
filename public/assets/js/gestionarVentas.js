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
            "lengthMenu": "Mostrando _MENU_ productos por página",
            "zeroRecords": "Ningún producto coincide con esas credenciales",
            "info": "Mostrando página <b>_PAGE_</b> de _PAGES_",
            "infoEmpty": "No hay productos disponibles",
            "infoFiltered": "(filtrado de <b>_MAX_</b> productos totales)"
        }
    });
    return dataTable;
}

/**
 * Función que saca una notificación con JQuery UI
 * @param {string} tituloNotificacion Título que tendrá el cuadro de diálogo
 * @param {string} textoNotificacion Texto que tendrá el cuerpo del cuadro de diálogo
 */
function notificar(tituloNotificacion, textoNotificacion) {

    // cambio el titulo y el texto en función de lo que se pasa por parámetro
    document.querySelector('#notificacion').title = tituloNotificacion;
    document.querySelector('#notificacion').innerHTML = textoNotificacion;

    // se abre una notificación de la acción que se ha realizado mediante JQuery UI dialog
    $(function () {
        $("#notificacion").dialog({
            modal: true,
            buttons: {
                Ok: function () {
                    // si hace click en Ok, se cierra la ventana
                    $(this).dialog("close");
                }
            }
        });
    });
}

function cargarTablaVentas() {
    $.ajax({
        type: "POST",
        url: "GestionarVentas/obtenerArrayProductos",
        dataType: "JSON",
        success: function (arrayProductos) {

            // creo la tabla
            const tablaVentas = document.createElement('table');
            tablaVentas.id = 'tablaVentas';
            tablaVentas.className = 'display';
            tablaVentas.style.width = '100%';

            // creo thead, tbody y tfoot
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');

            tablaVentas.appendChild(thead);
            tablaVentas.appendChild(tbody);

            for (let i = 0; i < arrayProductos.length; i++) {
                // cabeceras de la tabla, las meto en thead
                if (i == 0) {
                    crearCelda(arrayProductos[i], 'thead', thead);
                }
                else {
                    crearCelda(arrayProductos[i], 'tbody', tbody);
                }
            }

            // muestro los contenedores que había escondido, ya que la tabla ya está cargarda
            const containerTablaVentas = document.querySelector('#containerTablaVentas');
            containerTablaVentas.appendChild(tablaVentas);
            containerTablaVentas.style.display = 'block';

            let containerBtnVolver = document.querySelector('#containerBtnVolver');
            containerBtnVolver.style.display = 'block';

            // aplico dataTable a la tabla de productos
            let dataTableVentas = crearDataTable('#tablaVentas');


        }
    });
}

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

                    // creo el input de cantidad
                    const inputCantidad = document.createElement('input');
                    inputCantidad.type = 'number';
                    inputCantidad.min = '1';


                    // creo el botón de añadir al carrito para poder procesar ventas
                    const btnCarrito = document.createElement('button');
                    btnCarrito.className = 'btnCarrito';
                    btnCarrito.innerHTML = '<i class="bi bi-basket2"></i>';

                    // añado el evento de borrado
                    btnCarrito.addEventListener('click', function () {
                        // si el input de cantidad es 0, lo notifico
                        if (inputCantidad.value <= 0) {
                            notificar('Cantidad no válida', 'No se puede comprar esa cantidad de productos');
                        }
                        else {
                            // guardo los valores de la fila que me interesan
                            const idProducto = btnCarrito.parentNode.parentNode.querySelectorAll('td')[0].innerText;
                            const nombreProducto = btnCarrito.parentNode.parentNode.querySelectorAll('td')[1].innerText;
                            const cantidadProducto = inputCantidad.value;
                            const precioProducto = (cantidadProducto * btnCarrito.parentNode.parentNode.querySelectorAll('td')[4].innerText) + '€';

                            // los guardo en un array
                            const datosProducto = [
                                idProducto, nombreProducto, cantidadProducto, precioProducto
                            ];

                            // petición que comprueba si ese producto tiene suficiente stock
                            $.ajax({
                                type: "POST",
                                url: "GestionarVentas/comprobarStock",
                                data: { idProducto: idProducto, cantidadProducto: cantidadProducto },
                                dataType: "JSON",
                                success: function (response) {

                                    if (!response) {
                                        notificar('No se puede añadir al carrito', 'No hay suficiente stock de <b>' + nombreProducto + '</b> en el almacén');
                                    }
                                    else {
                                        // creo la tabla de carrito
                                        tablaCarrito(datosProducto);
                                    }
                                    // dejo el input de cantidad vacío
                                    inputCantidad.value = '';

                                },
                                error: function (error) {
                                    console.log(error);
                                }
                            });
                        }
                    });

                    // los añado a la celda
                    celda.appendChild(inputCantidad);
                    celda.appendChild(btnCarrito);
                }
                tr.appendChild(celda);

                break;
        }
    });
    parte.appendChild(tr);
}

function tablaCarrito(datosProducto) {

    // si la tabla no está creada, la creo
    if (!document.querySelector('#tablaCarrito')) {
        // creo el contenedor del carrito (div)
        const containerTablaCarrito = document.createElement('div');
        containerTablaCarrito.className = 'container';
        containerTablaCarrito.id = 'containerTablaCarrito';

        // creo la tabla
        const tablaCarrito = document.createElement('table');
        tablaCarrito.id = 'tablaCarrito';
        tablaCarrito.className = 'display';
        tablaCarrito.style.width = '100%';

        // creo el thead y el tbody y los añado a la tabla
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');
        const tfoot = document.createElement('tfoot');

        tablaCarrito.appendChild(thead);
        tablaCarrito.appendChild(tbody);
        tablaCarrito.appendChild(tfoot);

        containerTablaCarrito.appendChild(tablaCarrito);
        document.body.appendChild(containerTablaCarrito);

        // vuelvo a añadir el resto de contenedores para que el del carrito salga el primero
        document.body.appendChild(document.querySelector('#containerTablaVentas'));
        document.body.appendChild(document.querySelector('#containerBtnVolver'));

        let trPrincipal = document.createElement('tr');
        let thPrincipal = document.createElement('th');
        thPrincipal.colSpan = 4;
        thPrincipal.id = 'thPrincipal';
        thPrincipal.innerHTML = 'CARRITO <i class="bi bi-basket2"></i>';

        trPrincipal.appendChild(thPrincipal)
        thead.appendChild(trPrincipal);

        // cabeceras que tendrá la tabla
        let cabeceraCarrito = [
            'ID', 'NOMBRE', 'CANTIDAD', 'PRECIO'
        ]

        // creo el tr del thead
        let tr = document.createElement('tr');

        // creo los th del thead
        cabeceraCarrito.forEach(cabecera => {
            const th = document.createElement('th');
            th.innerText = cabecera;
            tr.appendChild(th);
        });
        thead.appendChild(tr);

        // creo el tr del tbody con el producto pasado por parámetro
        tr = document.createElement('tr');
        // creo los td del tbody
        datosProducto.forEach(dato => {
            const td = document.createElement('td');
            td.innerText = dato;
            tr.appendChild(td);
        })
        tbody.appendChild(tr);

        // creo el tfoot
        tr = document.createElement('tr');
        const thTotal = document.createElement('th');
        thTotal.innerText = 'PRECIO TOTAL';
        thTotal.colSpan = '3';
        thTotal.style.textAlign = 'center';
        tfoot.appendChild(thTotal);


        const thPrecio = document.createElement('th');
        thPrecio.id = 'precioTotal'
        thPrecio.innerText = datosProducto[3];
        tfoot.appendChild(thPrecio);

        const btnConfirmarCompra = document.createElement('button');
        btnConfirmarCompra.className = 'btnConfirmarCompra';
        btnConfirmarCompra.innerHTML = 'Confirmar Compra <i class="bi bi-check-circle-fill"></i>';

        btnConfirmarCompra.addEventListener('click', function () {
            confirmarAccion('¿Estás seguro de que quieres <b>realizar</b> la compra?', 'confirmarCompra');
        })

        const btnCancelarCompra = document.createElement('button');
        btnCancelarCompra.className = 'btnCancelarCompra';
        btnCancelarCompra.innerHTML = 'Cancelar compra <i class="bi bi-x-circle-fill"></i>';

        btnCancelarCompra.addEventListener('click', function () {
            confirmarAccion('¿Estás seguro de que quieres <b>cancelar</b> la compra?', 'cancelarCompra')
        })


        containerTablaCarrito.appendChild(btnConfirmarCompra);
        containerTablaCarrito.appendChild(btnCancelarCompra);
    }
    // si la tabla ya está creada, le meto el nuevo tr al tbody y calculo el nuevo total
    else {
        agregarProductoCarrito(datosProducto)
    }
}

function agregarProductoCarrito(datosProducto) {
    // Obtener la tabla y los datos del producto
    var tabla = document.getElementById("tablaCarrito").getElementsByTagName('tbody')[0];

    // Crear un nuevo elemento de fila y llenarlo con los datos del producto
    var nuevaFila = tabla.insertRow(-1);
    datosProducto.forEach(function (dato) {
        var celda = nuevaFila.insertCell();
        celda.textContent = dato;
    });

    // Calcular el precio total
    var precioTotal = calcularPrecioTotal();
    // Actualizar el precio total en el pie de la tabla
    document.getElementById("precioTotal").textContent = precioTotal;
}

function calcularPrecioTotal() {
    var filas = document.getElementById("tablaCarrito").getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    var precioTotal = 0;
    for (var i = 0; i < filas.length; i++) {
        var precioFila = parseFloat(filas[i].getElementsByTagName('td')[3].textContent); // índice 3 para el precio
        precioTotal += precioFila;
    }
    return precioTotal.toFixed(2) + "€"; // Redondear el precio total a 2 decimales
}

/**
 * Función que saca una confirmación con JQuery UI
 * @param {string} textoConfirmacion Texto que tendrá el cuerpo del cuadro de diálogo
 * @param {string} accion Indica a qué función llamar al hacer click en confirmar
 */
function confirmarAccion(textoConfirmacion, accion) {

    // coloco el texto en función de la accion que se quiere realizar
    document.querySelector('#confirmarAccion').innerHTML = textoConfirmacion;

    // se abre la confirmacion de la accion que se va a realizar mediante JQuery UI dialog
    $(function () {
        $("#confirmarAccion").dialog({
            modal: true,
            buttons: {

                // se llama a la función dependiendo de la accion pasada por parámetro
                Confirmar: function () {
                    switch (accion) {
                        case 'confirmarCompra':
                            // le paso el botón clickado para acceder a los td's
                            confirmarCompra();
                            break;
                        case 'cancelarCompra':
                            // hago un remove de la tabla
                            document.querySelector('#containerTablaCarrito').remove();
                            break;
                    }
                    // al realizar la acción, cierro el diáogo
                    $(this).dialog("close");
                },
                // se cierra el diálogo
                Cancelar: function () {
                    $(this).dialog("close");
                }
            }
        });
    });
}
function confirmarCompra() {
    // guardo todos los trs del tbody
    const trsProductos = document.querySelectorAll('#tablaCarrito>tbody>tr')

    // array con todos los datos de los productos que se han comprado, para descontar el stock
    arrayProductosVenta = [];

    // por cada tr, guardo todos los tds
    trsProductos.forEach(tr => {
        const td = tr.querySelectorAll('td');
        // array de cada producto que se ha comprado, con los datos para poder descontar el stock
        const arrayProducto = [
            td[0].innerText,
            td[2].innerText
        ]

        // hago el push
        arrayProductosVenta.push(arrayProducto)
    });
    console.log(arrayProductosVenta);

    // precio total de la venta
    const precioTotal = document.querySelector('#precioTotal').innerText;

    $.ajax({
        type: "POST",
        url: "GestionarVentas/procesarVenta",
        data: { arrayProductosVenta: arrayProductosVenta, precioTotal: precioTotal },
        dataType: "JSON",
        success: function (response) {

            if (response.status == 'success') {
                notificar('Venta procesada', response.message);
                document.querySelector('#containerTablaCarrito').remove();
            }
            else {
                notificar('Ha habido un error', response.message);
            }
        },
        error: function (error) {
            console.log(error);
        }
    });


    // AJAX QUE INSERTE EN AMBAS TABLAS TODO

    // AL FINAL, COOKIE PARA NOTIFICAR QUE SE HA REALIZADO CON ÉXITO

}

const containerTablaVentas = document.querySelector('#containerTablaVentas');
containerTablaVentas.style.display = 'none';

const containerBtnVolver = document.querySelector('#containerBtnVolver');
containerBtnVolver.style.display = 'none';

cargarTablaVentas();
