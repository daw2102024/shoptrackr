<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Configuración de Usuario</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- cdn bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- cdn's jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- cdn's jquery ui -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/theme.min.css">


    <!-- js -->
    <script defer src="/assets/js/configuracionUsuario.js"></script>
    <script defer src="/assets/js/funcionesComunes.js"></script>

    <!-- css -->
    <link rel="stylesheet" href="/assets/css/general.css">
    <link rel="stylesheet" href="/assets/css/cabecera.css">
    <link rel="stylesheet" href="/assets/css/configuracionUsuario.css">
</head>

<body>

    <?php
    // inicializo el servicio de sesion
    $session = \Config\Services::session();

    ?>

    <div class="container" id="containerHeader">
        <div id="containerLogoHeader">
            <a href="/">
                <img id="smallLogo" src="/assets/img/smallLogo.png" alt="logo shoptrackr">
            </a>
        </div>

        <h2>Configuración de usuario</h2>

        <div id="informacionUsuario">
            <span id="informacionSesion">
                <?= $session->get('nombre') ?>
                <span id="guionInformacionSesion">-</span>
                <?= $session->get('cargo') ?>
            </span>
            <i class="bi bi-person-circle"></i>
        </div>

    </div>

    <div class="container" id="containerOpciones">
        <div class="opcion" id="cambiarUsername">
            Cambiar nombre de usuario
        </div>

        <div class="opcion" id="cambiarNombre">
            Cambiar nombre y apellidos
        </div>


        <!-- si hay sesion de gerente, esconder estos 2 -->
        <!-- podria rentar tambien renombrar gestion de productos a ver productos -->
        <div class="opcion" id="cambiarPassword">
            Cambiar contraseña
        </div>
    </div>

    <div class="container" id="containerBtnVolver">
        <a href="/">
            <button class='btn'>Volver al menú principal</button>
        </a>
        <button class='btn' id='btnCerrarSesion'>Cerrar sesión</button>

    </div>

    <!-- JQuery UI dialog  -->
    <!-- alerta que sale al tocar sobre el usuario, para indicar qué acción realiar -->
    <div style="display: none;" id="notificacion" title="Acciones de usuario">
    </div>

    <!-- para notificar problemas, meto el texto con js dependiendo de qué problema sea -->
    <div style="style: display: none;" id="notificacion" title="Notificación">

    </div>

    <!-- confirmación de inserción de fila en BD -->
    <div style="" id="confirmarAccion" title="Confirmación">

    </div>

</body>

</html>