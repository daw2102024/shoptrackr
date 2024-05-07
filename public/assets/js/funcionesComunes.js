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
        btn.innerHTML = '<i class="bi bi-eye-slash-fill"></i>';
    }
    else if (btn.id == 'ocultarPassword') {
        inputPassword.type = 'password';
        btn.id = 'mostrarPassword';

        // cambio el boostrap icon
        btn.innerHTML = '<i class="bi bi-eye-fill"></i>';

    }
}

/**
 * Función que abre un diálogo en el que indicar si ir a la configuración de usuario o cerrar la sesión
 */
function abrirAlertaUsuario() {
    // abro la alerta
    $(function () {
        $("#alertaUsuario").dialog({
            modal: true,
            buttons: {
                // al hacer click en este botón, redirige a la configuración de usuario
                'Configuración de usuario': function () {

                    window.location.href = '/configuracionUsuario';
                },
                // cierra la sesión
                'Cerrar sesión': function () {

                    cerrarSesion();
                }
            }
        });
    });
}

/**
 * Función que cierra la sesión del usuario
 */
function cerrarSesion() {
    // creo una cookie de sesionCerrada, así al redirigir a "/", puedo mostrar un mensaje
    document.cookie = "sesionCerrada=true; path=/";

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

// Si hay botones de mostrar/ocultar contraseña
if (document.querySelectorAll('.btnOjo')) {
    const btnsOjo = document.querySelectorAll('.btnOjo');

    btnsOjo.forEach(btn => {
        btn.addEventListener('click', function () {
            mostrarOcultarPassword(btn);
        });
    });
}

// Si existe el icono de usuario, abro la alerta que indica que acciones se pueden realizar
if (document.querySelector('.bi-person-circle')) {
    // añado un evento que abre un díalogo al pulsar sobre el icono de usuario
    const zonaUsuario = document.querySelector('.bi-person-circle');
    zonaUsuario.addEventListener('click', function () {
        abrirAlertaUsuario();
    });
}

// Si existe el boton de cerrar sesión, llamo a su función
if (document.querySelector('#btnCerrarSesion')) {
    const btnCerrarSesion = document.querySelector('#btnCerrarSesion');

    btnCerrarSesion.addEventListener('click', function () {
        cerrarSesion();
    })
}

