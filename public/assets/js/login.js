/**
 * Función que devuelve el valor de una cookie
 * @param {string} nombre Nombre de la cookie
 * @return {Object} Valor de la cookie, en el caso de no existir, devuelve null
 */
function getCookie(name) {
    const cookieString = document.cookie;
    const cookies = cookieString.split('; ');
    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i].split('=');
        if (cookie[0] === name) {
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
 * Función que realiza una petición asíncrona para comprobar las credenciales introducidas
 */
function comprobarCredencialesInicioSesion() {

    // inputs de usuario y contraseá
    const inputUser = document.querySelector('#inputUser');
    const inputPassword = document.querySelector('#inputPassword');


    console.log(inputUser.value);
    console.log(inputPassword.value);

    // petición asíncrona que comprueba si las credenciales son correctas en función de los inputs
    $.ajax({
        type: "POST",
        // además de comprobar las credenciales, si son correctas crea la sesión
        url: "/Login/login",
        data: { user: inputUser.value, pass: inputPassword.value },
        dataType: 'JSON',
        success: function (response) {

            // si son válidas, redirige a menu
            if (response == true) {
                console.log(response);
                window.location.href = "/";

            }
            // si no son válidas, lo notifica
            else {

                // dialog de JQuery UI, notifica que las credenciales son incorrecto
                $(function () {
                    $("#notificarLoginIncorrecto").dialog({
                        modal: true,
                        buttons: {
                            Ok: function () {
                                $(this).dialog("close");
                            }
                        }
                    });
                });
            }
        },
        error: function (response) {
            console.log(response);
        }
    });
}

// si existe esta cookie, significa que se ha cerrado la sesión
if (getCookie('sesionCerrada')) {
    // borro la cookie
    borrarCookie('sesionCerrada');
    // notifico que la sesión ha sido cerrada
    notificar('Notificación', 'Se ha cerrado la sesión con éxito');

};

// añado el evento de inicio de sesión al boton de login
const btnLogin = document.querySelector('#btnLogin');
btnLogin.addEventListener('click', function () {
    comprobarCredencialesInicioSesion();
});



