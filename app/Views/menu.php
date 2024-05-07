<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Menú</title>

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
    <script defer src="/assets/js/funcionesComunes.js"></script>
    <script defer src="/assets/js/menu.js"></script>

    <!-- css -->
    <link rel="stylesheet" href="/assets/css/general.css">
    <link rel="stylesheet" href="/assets/css/cabecera.css">
    <link rel="stylesheet" href="/assets/css/menu.css">
</head>

<body>

    <?php
    // inicializo el servicio de sesion
    $session = \Config\Services::session();

    ?>

    <div class="container" id="containerHeader">
        <div id="containerLogoHeader">
            <img id="smallLogo" src="/assets/img/smallLogo.png" alt="logo shoptrackr">
        </div>

        <h2>Menú principal</h2>

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
        <a href="/gestionarVentas">
            <div class="opcion" id="gestionarVentas">
                Gestión de ventas
            </div>
        </a>

        <a href="/gestionarProductos">
            <div class="opcion" id="gestionarProductos">
                Gestión de productos
            </div>
        </a>


        <!-- si hay sesion de gerente, esconder estos 2 -->
        <!-- podria rentar tambien renombrar gestion de productos a ver productos -->
        <a href="/gestionarEmpleados">
            <div class="opcion" id="gestionarEmpleados">
                Gestión de empleados
            </div>
        </a>

        <a href="/movimientos">
            <div class="opcion" id="movimientos">
                Ver movimientos
            </div>
        </a>
    </div>

    <!-- si no hay alertas de stock, que salga esto -->
    <div class="container" id="sinNotificaciones">
        <h3>Sin notificaciones <i class="bi bi-bell-fill"></i></h3>
    </div>

    <!-- JQuery UI dialog -->
    <!-- alerta que sale al tocar sobre el usuario, para indicar qué acción realiar -->
    <div style="display: none;" id="alertaUsuario" title="Acciones de usuario">
        ¿Qué quieres hacer?
    </div>

</body>

</html>