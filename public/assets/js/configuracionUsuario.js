/**
 * Función que cierra la sesión del usuario
 */
function cerrarSesionSinAlerta() {

    // petición asíncrona que cierra la sesión
    $.ajax({
        type: "POST",
        url: "FuncionesGenerales/cerrarSesion",
        dataType: 'JSON',
        success: function (response) {
            console.log(response);
            // si devuelve true, significa que se ha borrado con éxito
            if (response == true) {
                // redirijo al login
                window.location.href = '/login';
            }
        },
        error: function (error) {
            console.log(error);
        }
    });
}

/**
 * Función que aplica la funcionalidad de mostrar/ocultar contraseña.
 * @param {Object} btn Botón al que se le aplica la función
 */
function mostrarOcultarPassword(btn) {
    // obtengo el respectivo input
    const inputPassword = btn.parentNode.querySelector('input');

    // en función de la clase, muestro o escondo el contenido del input
    if (btn.id == 'mostrarPassword') {
        inputPassword.type = 'text';
        btn.id = 'ocultarPassword';

        // cambio el boostrap icon
        btn.innerHTML = '<i class="bi bi-eye-fill"></i>';
    }
    else if (btn.id == 'ocultarPassword') {
        inputPassword.type = 'password';
        btn.id = 'mostrarPassword';

        // cambio el boostrap icon
        btn.innerHTML = '<i class="bi bi-eye-slash-fill"></i>';
    }
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



function crearFormulario(funcion) {

    if (document.querySelector('#containerFormularioAccion')) {
        document.querySelector('#containerFormularioAccion').remove();
    }

    document.querySelector('#containerOpciones').className = document.querySelector('#containerOpciones').className + ' small';

    const formularioAccion = document.createElement('div');
    formularioAccion.className = 'container';
    formularioAccion.id = 'containerFormularioAccion';


    document.body.appendChild(formularioAccion);

    // Hago el el div del botón de volver esté abajo del todo
    const containerBtnVolver = document.querySelector('#containerBtnVolver');
    document.body.appendChild(containerBtnVolver);

    let textoConfirmacion;

    switch (funcion) {
        case 'cambiarUsername':
            // defino el texto de confirmación que saldrá en la alerta
            textoConfirmacion = '¿Estás seguro de que quieres cambiar tu nombre de usuario?';
            crearInputs(
                'Formulario de cambio de nombre de usuario',
                [
                    [{ labelText: 'Nuevo nombre de usuario:', inputType: 'text' }],
                    [{ labelText: 'Contraseña:', inputType: 'password' }]
                ],
                function () {
                    // llamo a la función que muestra un diálogo de confirmación, le paso el texto de confirmación
                    confirmarAccion(textoConfirmacion, 'cambiarUsername');
                }
            );
            break;

        case 'cambiarNombre':
            // defino el texto de confirmación que saldrá en la alerta
            textoConfirmacion = '¿Estás seguro de que quieres cambiar tu nombre y apellidos?';
            crearInputs(
                'Formulario de cambio de nombre y apellidos',
                [
                    [{ labelText: 'Nuevo nombre y apellidos:', inputType: 'text' }],
                    [{ labelText: 'Contraseña:', inputType: 'password' }]
                ],
                function () {
                    // llamo a la función que muestra un diálogo de confirmación, le paso el texto de confirmación
                    confirmarAccion(textoConfirmacion, 'cambiarNombre');
                }
            );
            break;

        case 'cambiarPassword':
            // defino el texto de confirmación que saldrá en la alerta
            textoConfirmacion = '¿Estás seguro de que quieres cambiar tu contraseña?';
            crearInputs(
                'Formulario de cambio de contraseña',
                [
                    [{ labelText: 'Contraseña actual:', inputType: 'password' }],
                    [{ labelText: 'Nueva contraseña:', inputType: 'password' }, { labelText: 'Repite la nueva contraseña:', inputType: 'password' }]
                ],
                function () {
                    // llamo a la función que muestra un diálogo de confirmación, le paso el texto de confirmación
                    confirmarAccion(textoConfirmacion, 'cambiarPassword');
                }
            );
            break;
    }
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
                        case 'cambiarUsername':
                            // cambia el nombre de usuario en la BD
                            cambiarUsernameBD();
                            break;

                        case 'cambiarNombre':
                            // cambia el nombre del usuario en la BD
                            cambiarNombreBD();
                            break;

                        case 'cambiarPassword':
                            // cambia la contraseña del usuario en la BD
                            cambiarPasswordBD();
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

function cambiarUsernameBD() {
    // guardo los valores de los inputs
    const nuevoUsername = document.querySelectorAll('input')[0].value;
    const password = document.querySelectorAll('input')[1].value;


    // si el nuevo nombre de usuario está vacío, muestro una alerta
    if (nuevoUsername == '' || nuevoUsername == ' ') {
        notificar('Ha habido un problema', 'El <b>nombre de usuario</b> no puede estar <b>vacío</b>');
    }
    else {
        // petición asíncrona que comprueba si la contraseña es correcta y cambia el nombre de usuario en función del usuario con la sesión abierta
        $.ajax({
            type: "POST",
            url: "ConfiguracionUsuario/cambiarUsernameBD",
            data: { nuevoUsername: nuevoUsername, password: password },
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                if (response.status == 'error') {
                    notificar('Error al cambiar el nombre de usuario', response.message);
                }
                else {
                    notificar('Nombre de usuario cambiado', response.message);
                    setTimeout(() => {
                        cerrarSesionSinAlerta();
                    }, 3000);
                }
            },
            error: function (error) {
                console.log(error)
            }
        });
    }
}

function cambiarNombreBD() {
    // guardo los valores de los inputs
    const nuevoNombre = document.querySelectorAll('input')[0].value;
    const password = document.querySelectorAll('input')[1].value;

    // si el nuevo nombre está vacío, muestro una alerta
    if (nuevoNombre == '' || nuevoNombre == ' ') {
        notificar('Ha habido un problema', 'El <b>nombre</b> no puede estar <b>vacío</b>');
    }
    else {
        // petición asíncrona que comprueba si la contraseña es correcta y cambia el nombre del usuario en función del usuario con la sesión abierta
        $.ajax({
            type: "POST",
            url: "ConfiguracionUsuario/cambiarNombreBD",
            data: { nuevoNombre: nuevoNombre, password: password },
            dataType: "JSON",
            success: function (response) {
                console.log(response);
                if (response.status == 'error') {
                    notificar('Error al cambiar el nombre y apellidos', response.message);
                }
                else {
                    notificar('Nombre y apellidos cambiados', response.message);
                    setTimeout(() => {
                        cerrarSesionSinAlerta();
                    }, 3000);

                }
            }
        });
    }
}

function cambiarPasswordBD() {
    // guardo los valores de los inputs
    const passwordActual = document.querySelectorAll('input')[0].value;
    const passwordNueva = document.querySelectorAll('input')[1].value;
    const passwordNuevaRepetir = document.querySelectorAll('input')[2].value;

    // si el nuevo nombre está vacío, muestro una alerta
    if (passwordActual == '' || passwordActual == ' ') {
        notificar('Ha habido un problema', 'La <b>nueva contraseña</b> no puede estar <b>vacía</b>');
    }
    else {
        // petición asíncrona que comprueba si la contraseña actual es correcta, si la nueva contraseña coincide en los 2 inputs, y cambia la contraseña en función del usuario con la sesión abierta
        $.ajax({
            type: "POST",
            url: "ConfiguracionUsuario/cambiarPasswordBD",
            data: { passwordActual: passwordActual, passwordNueva: passwordNueva, passwordNuevaRepetir: passwordNuevaRepetir },
            dataType: "JSON",
            success: function (response) {

                if (response.status == 'error') {
                    notificar('Error al cambiar la contraseña', response.message);
                }
                else {
                    notificar('Contraseña cambiada', response.message);


                    setTimeout(() => {
                        cerrarSesionSinAlerta();
                    }, 3000);

                }
            },
            error: function (error) {
                console.log(error)
            }
        });
    }
}

function crearInputs(titulo, campos, onSubmit) {
    let formularioAccion = document.querySelector('#containerFormularioAccion');
    formularioAccion.innerHTML = '<h2>' + titulo + '</h2>';

    campos.forEach(camposGrupo => {
        const divGrupo = document.createElement('div');
        divGrupo.classList.add('divInput');
        camposGrupo.forEach(({ labelText, inputType }) => {
            const label = document.createElement('label');
            label.textContent = labelText;

            const input = document.createElement('input');
            input.type = inputType;

            divGrupo.appendChild(label);

            if (inputType == 'password') {
                const divInputPassword = document.createElement('div');
                divInputPassword.className = 'divInputPassword';

                const btnOjo = document.createElement('button');
                btnOjo.className = 'btnOjo';
                btnOjo.id = 'mostrarPassword';
                btnOjo.innerHTML = '<i class="bi bi-eye-fill"></i>'

                divInputPassword.appendChild(input);
                divInputPassword.appendChild(btnOjo);

                divGrupo.appendChild(divInputPassword);

                btnOjo.addEventListener('click', function () {
                    mostrarOcultarPassword(btnOjo);

                })

                // EVENTO DEL OJO
                // PROBAR QUITANDO ESTILOS

            }
            else {
                divGrupo.appendChild(input);
            }
        });
        formularioAccion.appendChild(divGrupo);
    });

    const botonSubmit = document.createElement('button');
    botonSubmit.className = 'btn';
    botonSubmit.id = 'btnEnviar';
    botonSubmit.textContent = 'Aplicar cambios';

    botonSubmit.addEventListener('click', onSubmit);

    formularioAccion.appendChild(botonSubmit);
}

// a todas las opciones les meto un eventListener, generan un formulario diferente dependiendo de su función
let divPassword = '';
let labelPassword = '';
let inputPassword = '';

const opciones = document.querySelectorAll('.opcion');
opciones.forEach(opcion => {
    opcion.addEventListener('click', function () {

        switch (opcion.id) {
            case 'cambiarUsername':
                crearFormulario('cambiarUsername');
                break;

            case 'cambiarNombre':
                crearFormulario('cambiarNombre');
                break;

            case 'cambiarPassword':
                crearFormulario('cambiarPassword');
                break;
        }

    });

});


// obtener los inputs sin subselectores