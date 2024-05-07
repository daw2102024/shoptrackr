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
            "lengthMenu": "Mostrando _MENU_ empleados por página",
            "zeroRecords": "Ningún empleado coincide con esas credenciales",
            "info": "Mostrando página <b>_PAGE_</b> de _PAGES_",
            "infoEmpty": "No hay empleados disponibles",
            "infoFiltered": "(filtrado de <b>_MAX_</b> empleados totales)"
        }
    });
    return dataTable;
}

/**
 * Función que crea una cookie, no me hace falta fecha de expiración ya que la borraré al usarla
 * @param {string} nombre Nombre de la cookie que se creará
 * @param {string} valor Valor de la cookie que se creará
 */
function setCookie(nombre, valor) {
    document.cookie = nombre + "=" + valor + ";path=/";
}

/**
 * Función que devuelve el valor de una cookie
 * @param {string} nombre Nombre de la cookie
 * @return {Object} Valor de la cookie, en el caso de no existir, devuelve null
 */
function getCookie(nombre) {
    const cookieString = document.cookie;
    const cookies = cookieString.split('; ');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].split('=');
        if (cookie[0] === nombre) {
            return cookie[1];
        }
    }
    return null;
}

/**
* Función que borra una cookie en función del nombre
* @param {string} nombre Nombre de la cookie que se borrará
*/
function borrarCookie(nombre) {
    document.cookie = nombre + '=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
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

/**
* Función que crea la tabla de empleados y aplica dataTable
*/
function cargarTablaEmpleados() {

    $.ajax({
        type: "POST",
        url: "GestionarEmpleados/obtenerArrayEmpleados",
        dataType: "JSON",
        success: function (arrayEmpleados) {

            let containerTablaEmpleados = document.querySelector('#containerTablaEmpleados');

            // creo la tabla
            const tablaEmpleados = document.createElement('table');
            tablaEmpleados.id = 'tablaEmpleados';
            tablaEmpleados.className = 'display';
            tablaEmpleados.style.width = '100%';

            // creo thead, tbody y tfoot
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');
            const tfoot = document.createElement('tfoot');

            // creo el botón de exportar a excel
            const btnExportar = document.createElement('button');
            btnExportar.innerHTML = 'Exportar empleados a excel <i class="bi bi-file-earmark-spreadsheet-fill"></i>';
            btnExportar.id = 'btnExportar';

            // añado el evento de exportar a excel
            btnExportar.addEventListener('click', function () {
                exportarExcel(dataTableEmpleados, '#tablaEmpleados', 'Tabla de empleados');
            });

            containerTablaEmpleados.appendChild(btnExportar);

            tablaEmpleados.appendChild(thead);
            tablaEmpleados.appendChild(tbody);
            tablaEmpleados.appendChild(tfoot);

            // recorro el array de empleados
            for (let i = 0; i < arrayEmpleados.length; i++) {
                // cabeceras de la tabla, las meto en thead
                if (i == 0) {
                    crearCelda(arrayEmpleados[i], 'thead', thead);
                    // creo también el tfoot (necesita los mismos datos que el thead para crearse)
                    crearCelda(arrayEmpleados[i], 'tfoot', tfoot);
                }
                else {
                    crearCelda(arrayEmpleados[i], 'tbody', tbody);
                }
            }

            // muestro los contenedores que había escondido, ya que la tabla ya está cargarda
            containerTablaEmpleados.appendChild(tablaEmpleados);
            containerTablaEmpleados.style.display = 'block';

            // muestro el container con el botón de volver
            let containerBtnVolver = document.querySelector('#containerBtnVolver');
            containerBtnVolver.style.display = 'block';

            // aplico dataTable a la tabla de empleados
            let dataTableEmpleados = crearDataTable('#tablaEmpleados');

            // aplico el evento de insertar al botón de insertar
            document.querySelector('#btnInsertar').addEventListener('click', function () {

                let selectCargo = document.querySelector('#selectCARGO');

                // si el select está marcado con el valor por defecto (value = 0)
                if (selectCargo.value == 0) {
                    // dialog de JQuery UI, notifica que hay que seleccionar una marca y categoría
                    notificar('Ha habido un problema', '<p>Por favor, selecciona <b>cargo</b> del empleado que quieres insertar</p>')

                }
                else {
                    // llamo a la función que saca una confirmación por pantalla
                    confirmarAccion(textoConfirmacion[0], 'insertar', btnInsertar);
                }

            })

        },
        error: function (error) {
            console.log(error);
        }
    });
}


/**
 * Función que crea las celdas (td) de la tabla de empleados
 * @param {Array} filaTabla Datos de un empleado
 * @param {string} tipo Indica a qué parte de la tabla pertenecerá la celda (thead, tbody o tfoot)
 * @param {Object} parte Parte de la tabla a la que pertenece la celda
 */
function crearCelda(filaTabla, tipo, parte) {
    const tr = document.createElement('tr');

    // Por cada empleado obtenido, creo las celdas
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

                    // creo el botón de borrar y le doy formato
                    const btnBorrar = document.createElement('button');
                    btnBorrar.className = 'btnBorrar';
                    btnBorrar.innerHTML = '<i class="bi bi-trash-fill"></i>';

                    // creo el botón de editar y le doy formato
                    const btnEditar = document.createElement('button');
                    btnEditar.className = 'btnEditar';
                    btnEditar.innerHTML = '<i class="bi bi-pencil-fill"></i>';

                    // añado el evento de borrado
                    btnBorrar.addEventListener('click', function () {

                        // paso el texto de confirmacion de borrado y todo lo necesario a la función que saca una confirmación por pantalla
                        confirmarAccion(textoConfirmacion[1], 'borrar', btnBorrar);
                    });

                    // añado el evento de editado
                    btnEditar.addEventListener('click', function () {

                        // le paso el boton a la función que sustituye los tds cuya información se puede modificar de un empleado por inputs
                        colocarInputsEditar(btnEditar)

                    });

                    // los añado a la celda
                    celda.appendChild(btnEditar);
                    celda.appendChild(btnBorrar);
                }
                tr.appendChild(celda);

                break;

            case 'tfoot':
                // al ser tfoot, creo los th
                celda = document.createElement('th');

                // si pertenecen a la columna de OPCIONES, creo inputs
                if (info != 'OPCIONES') {
                    // si son MARCA o CATEGORÍA, creo selects, ya que al ser foreign keys, es imprescindible que no haya error al insertar un nombre de marca que no existe
                    if (info == 'CARGO') {

                        const select = document.createElement('select');
                        // cambio el id para poder acceder a los valores
                        select.id = 'select' + info;

                        // declaro variables que usaré en el if
                        // aquí almacenaré a qué controlador llamar para obtener el array de marcas/categorías
                        let obtenerArrayControlador = '';
                        // opción marcada por defecto en los select
                        let optionDefault = '';

                        // creo la opción por defecto de Marca
                        optionDefault = document.createElement('option');
                        optionDefault.textContent = 'CARGO';
                        optionDefault.value = 0;
                        optionDefault.disabled = true;

                        // disabled para que no se pueda marcar
                        optionDefault.selected = true;

                        select.appendChild(optionDefault);

                        // realizo la petición asíncrona al controlador definido en obtenerArrayControlador
                        $.ajax({
                            type: "POST",
                            url: "GestionarEmpleados/obtenerArrayCargos",
                            dataType: "JSON",
                            success: function (arrayRespuesta) {
                                // por cada marca/categoría obtenida
                                arrayRespuesta.forEach(fila => {
                                    // creo el opction y lo añado al select
                                    const option = document.createElement('option');
                                    option.value = fila.id;
                                    option.textContent = fila.nombre;

                                    select.appendChild(option);
                                });
                                celda.appendChild(select);

                            },
                            error: function (error) {
                                console.log(error)
                            }
                        });
                    }
                    else {
                        // si se trata del resto de información que se puede editar, ya que no son FK
                        // creo inputs normales
                        const input = document.createElement('input');
                        // de placeholder dejo el nombre de la columna
                        input.placeholder = info;
                        // cambio el id para porder acceder a ellos más fácilmente
                        input.id = 'input' + info;

                        // cambio la anchura tanto de la celda como del input
                        if (info == 'ID' || info == 'PRECIO') {
                            celda.style.width = '10%';
                            input.style.width = '82%';
                        }
                        celda.appendChild(input);
                    }
                }
                // si se trata de la columna de opciones, añado el botón de insertar fila
                else {
                    const btnInsertar = document.createElement('button');
                    btnInsertar.id = 'btnInsertar';
                    btnInsertar.innerHTML = 'Crear empleado';
                    btnInsertar.style.width = '100%';
                    celda.appendChild(btnInsertar);
                }
                tr.appendChild(celda);

                break;
        }
    });
    parte.appendChild(tr);
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
        data: { 'datosTabla': JSON.stringify(datosTabla), archivo: archivo, ultimaColumna: 'G' },
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


/**
 * Función que coloca los inputs en las celdas de la fila a la que se ha clickado el botón de editar
 * @param {Object} btnEditar es el botón que se ha clickado, me hace falta para obtener la fila a la que pertenece el botón
 */
function colocarInputsEditar(btnEditar) {
    // obtengo el tr que corresponde al empleado que se quiere editar
    let fila = btnEditar.parentNode.parentNode;

    // obtengo todas las celdas de esa fila (de ese empleado)
    let celdas = fila.getElementsByTagName('td');

    // array con los valores de esa fila, para poder sustituir el innerText por inputs con el valor que estaba
    let valoresEmpleado = [];

    // resto 1 porque no me interesa la columna de OPCIONES
    for (let i = 0; i < celdas.length - 1; i++) {
        valoresEmpleado.push(celdas[i].innerText);
    }

    // empiezo en 1 y resto 1 porque no quiero poner inputs en las columnas de ID y OPCIONES
    for (let i = 1; i < celdas.length - 1; i++) {

        // si se trata de la columna de marcas y categorias, meto un select option para que no dé lugar a error
        if (i == 4) {

            // en funcion de si es marca o categoría, pillo un select u otro
            let selectEdicion = document.querySelector('#selectCARGO');

            // tengo que clonarlo para hacer el appendChild y que no se borre el que ya estaba
            let selectEdicionClonado = selectEdicion.cloneNode(true);
            selectEdicionClonado.className = 'inputEditar';
            selectEdicionClonado.id = '';

            // cojo todos los options de ese select para dejar seleccionado el valor de ese empleado
            let optionsEdicion = selectEdicionClonado.querySelectorAll('option');

            // sobre todos los options, si el innerText coincide con el valor del empleado, lo selecciono
            for (let j = 0; j < optionsEdicion.length; j++) {

                if (optionsEdicion[j].innerText === valoresEmpleado[i]) {
                    optionsEdicion[j].selected = true;
                    break; // salgo del bucle en cuanto encuentro ese valor
                }
            }

            // vacío el contenido
            celdas[i].innerHTML = '';
            celdas[i].appendChild(selectEdicionClonado);

        }
        // si son el resto de columnas, al no ser FKs, creo inputs normales
        else {
            celdas[i].innerHTML = '<input type="text" class="inputEditar" value="' + valoresEmpleado[i] + '">';
        }
    }

    // cambio los botones de opciones que ya estaban por los de Confirmar y Cancelar edición
    let tdOpciones = btnEditar.parentNode;
    console.log(tdOpciones);

    // quito los botones que ya estaban para meter los de confirmación y cancelación de edición
    let btnBorrar = tdOpciones.querySelector(".btnBorrar");
    console.log(btnBorrar);
    btnBorrar.remove();
    btnEditar.remove();

    // creo el nuevo botón de confirmar cambios
    const btnConfirmarCambios = document.createElement('button');
    btnConfirmarCambios.className = 'btnConfirmarCambios';
    // le meto un bootstrap icon
    btnConfirmarCambios.innerHTML = '<i class="bi bi-check-circle-fill"></i>';

    // creo el nuevo botón de cancelar cambios
    const btnCancelarCambios = document.createElement('button');
    btnCancelarCambios.className = 'btnCancelarCambios';
    // le meto un bootstrap icon
    btnCancelarCambios.innerHTML = '<i class="bi bi-x-circle-fill"></i>';

    // añado los eventos
    btnConfirmarCambios.addEventListener('click', function () {

        // saco un diálogo de confirmación por pantalla
        confirmarAccion(textoConfirmacion[2], 'editar', btnEditar);
    })

    btnCancelarCambios.addEventListener('click', function () {
        // cancelo los cambios
        cancelarCambios();
    })

    // añado los botones a la celda de opciones
    tdOpciones.appendChild(btnConfirmarCambios);
    tdOpciones.appendChild(btnCancelarCambios);
}


/**
 * Función que saca una confirmación con JQuery UI
 * @param {string} textoConfirmacion Texto que tendrá el cuerpo del cuadro de diálogo
 * @param {string} accion Indica a qué función llamar al hacer click en confirmar
 * @param {Object} btn Es el botón que se ha clickado, es necesario para obtener los inputs de la fila a la que pertenece el botón
 */
function confirmarAccion(textoConfirmacion, accion, btn) {

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
                        case 'insertar':
                            // inserta el empleado en la BD
                            insertarEmpleadoBD();
                            break;
                        case 'borrar':
                            // borra la empleado en la BD
                            // tengo que pasarle el btn para poder acceder a los inputs de esa fila
                            borrarEmpleadoBD(btn);
                            break;
                        case 'editar':
                            // edita la empleado en la BD
                            // tengo que pasarle el btn para poder acceder a los inputs de esa fila
                            editarEmpleadoBD(btn);
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


/**
 * Función que inserta un empleado con los datos de los inputs del tfoot en la BD
 */
function insertarEmpleadoBD() {
    // guardo los inputs del tfoot en variables para tratar la inromación más cómodamente
    let inputId = document.querySelector('#inputID');
    let inputNombre = document.querySelector('#inputNOMBRE');
    let inputUsername = document.querySelector('#inputUSERNAME');
    let inputPassword = document.querySelector('#inputPASSWORD');
    let selectCargo = document.querySelector('#selectCARGO');


    // guardo los valores de los inputs en un array
    let datosFila = [
        inputId.value,
        inputNombre.value,
        inputUsername.value,
        inputPassword.value,
        selectCargo.value
    ]

    // si hay campos sin rellenar, lo notifico
    if (inputId.value == '' || inputNombre.value == '' || inputUsername.value == '' || inputPassword.value == '' || selectCargo.value == '') {
        notificar('Ha habido un problema', '<p>Por favor, <b>rellena todos los campos</b> antes de insertar un nuevo empleado</p>')
    }
    // todas las comprobaciones del lado cliente hechas, hago una petición para hacer las comprobaciones con la BD e insertar la fila
    else {
        $.ajax({
            type: "POST",
            url: "GestionarEmpleados/insertarEmpleadoBD",
            // paso los valores de los inputs
            data: { datosFila: datosFila },
            dataType: "JSON",
            success: function (response) {
                // si es success, la inserción se ha realizado con éxito, saco una notificación informando al usuario con el mensaje
                if (response.status == 'success') {
                    // creo una cookie para notificar que la inserción se ha realizado con éxito
                    setCookie('accion', response.message)
                    // recargo la página
                    window.location.reload();
                }
                // si es failed, algo ha fallado al realizar la inserción, lo notifico y muestro el mensaje
                else if (response.status == 'failed') {
                    notificar('Ha habido un problema', response.message);
                }

            },
            error: function (error) {
                console.log(error);
            }
        });
    }

}

/**
 * Función borra un empleado de la base de datos
 * @param {Object} btnBorrar es el botón que se ha clickado, es necesario para obtener los valores de los tds (en este caso id y nombre) de esa fila
 */
function borrarEmpleadoBD(btnBorrar) {

    // obtengo el valor del td correspondiente a la ID y al nombre del empleado
    let idEmpleado = btnBorrar.parentNode.parentNode.getElementsByTagName('td')[0].innerText;
    // el nombre lo usaré para notificar qué empleado se ha borrado
    let nombreEmpleado = btnBorrar.parentNode.parentNode.getElementsByTagName('td')[1].innerText;

    // hago una petición asíncrona al controlador que borra el empleado de la base de datos
    $.ajax({
        type: "POST",
        url: "GestionarEmpleados/borrarEmpleadoBD",
        // paso el id y el nombre del empleado
        data: { idEmpleado: idEmpleado, nombreEmpleado: nombreEmpleado },
        dataType: "JSON",
        success: function (response) {
            // si es success, el borrado se ha realizado con éxito
            if (response.status == 'success') {
                // creo una cookie para notificar que el empleado se ha borrado con éxito
                setCookie('accion', response.message)
                // recargo la página para que se vuelva a cargar la tabla
                window.location.reload();
            }
            // si es failed, algo ha fallado al borrar el empleado, lo notifico y saco el mensaje
            else {
                notificar('Ha habido un problema', response.message);
            }
        },
        error: function (error) {
            console.log(error)
        }
    });
}

/**
* Función edita un empleado de la base de datos
*/
function editarEmpleadoBD() {
    // obtengo todos los inputs que corresponden a la edición
    const inputsEditar = document.querySelectorAll('.inputEditar');

    // el id no se obtiene de un input, por lo que lo obtengo por el innerText de su celda
    const idEmpleadoEditado = inputsEditar[0].parentNode.parentNode.querySelector('td').innerText;

    // array que guardará todos los datos que se quieren editar y se enviará al controlador encargado de editar la fila
    let datosEditados = [
        idEmpleadoEditado
    ];

    // bandera que indica si hay algún input vacío
    let inputVacio = false;

    // voy añadiendo los datos de los inputs en el array
    inputsEditar.forEach(inputEditar => {
        datosEditados.push(inputEditar.value);
        // si está vacío, marco la bandera con true
        if (inputEditar.value == '') {
            inputVacio = true;
        }
    });


    if (inputVacio) {
        notificar('Ha habido un error', 'Por favor, <b>rellena todos los campos</b> para editar un empleado');
    }
    else {
        // petición asíncrona que llama al controlador que edita el empleado de la BD
        $.ajax({
            type: "POST",
            url: "GestionarEmpleados/editarEmpleadoBD",
            // paso el array con los datos del empleado que se quiere editar
            data: { datosEditados: datosEditados },
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                // si es success, la edición se ha realizado con éxito
                if (response.status == 'success') {
                    // creo una cookie que permitirá notificar que el empleado ha sido editado
                    setCookie('accion', response.message);
                    // recargo la página para que se vuelvan a cargar los datos de la tabla
                    window.location.reload();
                }
                // si ha sido failed, lo notifico y saco el mensaje de error
                else {
                    notificar('Ha habido un problema', response.message);
                }
            },
            error: function (error) {
                console.log(error);
            }
        });
    }
}

/**
 * Función que cancela los cambios al editar un empleado (quita todos los inputs que se colocaron al pulsar en editar)
 */
function cancelarCambios() {

    // cambio el cuerpo del diálogo de confirmación
    document.querySelector('#confirmarAccion').innerHTML = '<p>¿Estás seguro de que quieres <b>cancelar la edición</b> de este empleado?</p>';

    // no utilizo la función de confirmar acción porque me interesa que esta tenga otros botones
    $(function () {
        $("#confirmarAccion").dialog({
            modal: true,
            buttons: {
                // cierra el diálogo
                'Seguir editando': function () {
                    $(this).dialog("close");
                },
                // recarga la página para quitar todos los inputs
                'Cancelar edición': function () {
                    window.location.reload();
                }
            }
        });
    });
}


const containerTablaEmpleados = document.querySelector('#containerTablaEmpleados');
containerTablaEmpleados.style.display = 'none';

const containerBtnVolver = document.querySelector('#containerBtnVolver');
containerBtnVolver.style.display = 'none';

// texto que se enviará a confirmarAccion indicando qué accion se quiere realizar
let textoConfirmacion = [
    'Se va a <b>insertar esta línea</b> en la Base de Datos, ¿estás seguro de que quieres hacerlo?',
    'Se va a <b>borrar esta línea</b> de la Base de Datos, ¿estás seguro de que quieres hacerlo?',
    'Se va a <b>editar esta línea</b> en la Base de Datos, ¿estás seguro de que quieres hacerlo?',
]

// inicializo la variable dataTable, donde almacenaré el objeto de dataTable tras cargar la tabla
let dataTableEmpleados = '';

// si hay cookie significa que tengo que mostrar un diálogo de JQuery UI notificando de algo (el valor de la cookie)
let accionRealizada = '';
if (accionRealizada = getCookie('accion')) {
    borrarCookie('accion');
    notificar('Acción realizada', accionRealizada);
}

cargarTablaEmpleados();