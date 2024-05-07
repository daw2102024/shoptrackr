<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gestión de productos</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- cdn's jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>

    <!-- cdn's jquery ui -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/theme.min.css">

    <!-- cdn's dataTables -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.3/css/foundation.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.foundation.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/foundation/6.4.3/js/foundation.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.5/js/dataTables.foundation.js"></script>

    <!-- cdn's bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- js -->
    <script defer src="/assets/js/funcionesComunes.js"></script>
    <script defer src="/assets/js/gestionarProductos.js"></script>

    <!-- css -->
    <link rel="stylesheet" href="/assets/css/general.css">
    <link rel="stylesheet" href="/assets/css/cabecera.css">
    <link rel="stylesheet" href="/assets/css/gestionarProductos.css">


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

        <h2>Gestión de productos</h2>

        <div id="informacionUsuario">
            <span id="informacionSesion">
                <?= $session->get('nombre') ?>
                <span id="guionInformacionSesion">-</span>
                <?= $session->get('cargo') ?>
            </span>
            <i class="bi bi-person-circle"></i>
        </div>

    </div>


    <div class="container" id="containerTablaProductos">
    </div>

    <div class="container" id="containerBtnVolver">
        <a href="/">
            <button class='btn'>Volver al menú principal</button>
        </a>
    </div>

    <!-- Formulario de carga de archivos, visibility hidden ya que se no se le puede dar estilos, y muestro un botón -->
    <input style="display: none" type="file" id="inputCargarStock">

    <!-- JQuery UI dialog  -->
    <!-- para notificar problemas, meto el texto con js dependiendo de qué problema sea -->
    <div style="style: display: none;" id="notificacion" title="Notificación">

    </div>


    <!-- confirmación de inserción de fila en BD -->
    <div style="" id="confirmarAccion" title="Confirmación">

    </div>

    <!-- alerta que sale al tocar sobre el usuario, para indicar qué acción realiar -->
    <div style="display: none;" id="alertaUsuario" title="Acciones de usuario">
        ¿Qué quieres hacer?
    </div>
</body>

</html>