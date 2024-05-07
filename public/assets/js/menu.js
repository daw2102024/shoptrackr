

/**
 * Función que permite aplicar el dataTable a un tabla
 * @param {Array} arrayProductos array con los productos que pertenecen a ese nivel de alerta
 * @param {string} nivelAlerta indica qué nivel de alerta es (cercaStockMinimo, debajoStockMinimo, sinStock)
 * @param {Object} containerNotificaciones Container de notificaciones, lo necesito para añadir la alerta al container
 */
function crearAlertaStock(arrayProductos, nivelAlerta, containerNotificaciones) {

    // quito el container que informa de que no hay notificaciones
    if (document.querySelector('#sinNotificaciones')) {
        document.querySelector('#sinNotificaciones').remove();
    }



    // creo el div de alerta y su titulo
    const alerta = document.createElement('div');
    alerta.className = 'container';

    let titulo = document.createElement('h5');

    // defino el id y el interior del div en función del nivel de alerta
    switch (nivelAlerta) {
        case 'cercaStockMinimo':
            alerta.id = 'alertaCercaStockMinimo';
            titulo.innerHTML = '<h4><i class="bi bi-exclamation-triangle-fill"></i>Productos <b>cerca de stock mínimo</b>:</h4>';
            break;

        case 'debajoStockMinimo':
            alerta.id = 'alertaDebajoStockMinimo';
            titulo.innerHTML = '<h4><i class="bi bi-exclamation-triangle-fill"></i>Productos por <b>debajo de stock mínimo</b>:</h4>';
            break;

        case 'sinStock':
            alerta.id = 'alertaSinStock';
            titulo.innerHTML = '<h4><i class="bi bi-exclamation-triangle-fill"></i>Productos <b>sin stock</b>:</h4>';
            break;
    }

    // añado el título al div de alerta
    alerta.appendChild(titulo);

    // añado los productos que pertenecen a esa alerta
    arrayProductos.forEach(producto => {
        alerta.innerHTML += '<p>' + producto['id'] + ' - ' + producto['nombre'] + '</p>';
    });
    // añado la alerta al div de notificaciones
    containerNotificaciones.appendChild(alerta);
}

/**
 * Función que realiza una petición asíncrona que comprueba el stock de todos los productos, para sacar alertas
 */
function comprobarStock() {
    $.ajax({
        type: "POST",
        url: "Menu/comprobarStocks",
        dataType: "JSON",
        success: function (response) {
            console.log(response);

            // creo el contenedor de notificaciones y lo añado al body
            const containerNotificaciones = document.createElement('div');
            containerNotificaciones.className = 'container';
            containerNotificaciones.id = 'notificaciones';
            containerNotificaciones.innerHTML = '<h3>Tienes notificaciones <i class="bi bi-bell-fill"></i></h3>';

            document.body.appendChild(containerNotificaciones);

            // Si hay productos con stock cerca del stock_minimo, saco una alerta
            if (response[0].length > 0) {
                crearAlertaStock(response[0], 'cercaStockMinimo', containerNotificaciones);
            }

            // si hay productos por debajo del stock minimo, saco una alerta
            if (response[1].length > 0) {
                crearAlertaStock(response[1], 'debajoStockMinimo', containerNotificaciones);
            }

            // si hay productos sin stock, saco una alerta
            if (response[2].length > 0) {
                crearAlertaStock(response[2], 'sinStock', containerNotificaciones);
            }
        },
        error: function (error) {
            console.log(error)
        }
    });
}

// petición asíncrona que comprueba si el usuario logeado es Gerente y en función muestra unas opciones u otras
$.ajax({
    type: "POST",
    url: "FuncionesGenerales/comprobarCargoGerente",
    dataType: "JSON",
    success: function (response) {
        cargoGerente = response;

        if (!cargoGerente) {
            document.querySelector('#gestionarEmpleados').remove();
            document.querySelector('#movimientos').remove();

            document.querySelectorAll('.opcion').forEach(opcion => {
                opcion.style.width = '400px';
            })
        }

    },
    error: function (response) {
        console.log(response);
    }
});


// llamo a la función que comprueba el stock al cargar la página
comprobarStock();

