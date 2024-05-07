<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inicio de sesión</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <!-- cdn's jquery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- cdn's jquery ui -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/theme.min.css">

    <!-- cdn bootstrap icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

    <!-- js -->
    <script defer src="/assets/js/login.js"></script>
    <script defer src="/assets/js/funcionesComunes.js"></script>

    <!-- css -->

    <link rel="stylesheet" href="/assets/css/general.css">
    <link rel="stylesheet" href="/assets/css/login.css">



</head>

<body>

    <div class="container" id="containerLogo">
        <img id="logo" src="/assets/img/logo.png" alt="logo shoptrackr">
        <div id="linea"></div>
        <div id="paginaActual">
            <h1>Inicio de sesión</h1>
        </div>
    </div>



    <div class="container" id="containerLogin">
        <div id="loginForm">
            <div id="divUsername">
                <label for="user">Nombre de usuario: </label>
                <input type="text" id="inputUser">
            </div>
            <div id="divPassword">
                <label for="user">Contraseña: </label>
                <div id="divInputPassword">
                    <input type="password" name="password" id="inputPassword">
                    <button class="btnOjo" id="mostrarPassword"><i class="bi bi-eye-fill"></i></button>
                </div>
            </div>
        </div>

        <button class='btn' id="btnLogin">Iniciar sesión</button>


    </div>

    <!-- JQuery UI dialog  -->
    <!-- para notificar login incorrecto -->
    <div style="display: none" id="notificarLoginIncorrecto" title="Credenciales incorrectas">
        <p>
            Nombre de usuario o contraseña incorrectos.
        </p>
    </div>

    <!-- JQuery UI dialog  -->
    <!-- para notificar, meto el texto con js dependiendo de lo que necesite -->
    <div style="display: none;" id="notificacion" title="Notificación">

    </div>

</body>

</html>