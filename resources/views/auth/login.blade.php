
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow - Login</title>

    <link rel="stylesheet" href="{{ asset('styles/style.css') }}">
</head>

<body>

<img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">

<div class="contenedor-login">

    <!-- LOGIN -->
    <div class="login active-panel" id="loginPanel">

        <img src="{{ asset('img/icono pantalla.png') }}" alt="Login Art" class="imagen_login">

        <div class="formulario-derecho">

            <h2>iniciar sesion</h2>
            @if ($errors->any())
    <div class="error-login">
        Correo o contraseña incorrectos
    </div>
@endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <input
                    type="email"
                    name="email"
                    placeholder="correo"
                    required
                >

                <input
                    type="password"
                    name="password"
                    placeholder="contraseña"
                    required
                >

                <button type="submit" id="btn-logindash">
                    iniciar sesion
                </button>

                <button type="button" onclick="mostrarRegister()">
                    registrarse
                </button>

            </form>

        </div>

    </div>

    <!-- REGISTER -->
    <div class="login hidden-right" id="registerPanel">

        <img src="{{ asset('img/fondo 3.png') }}" alt="Register Art" class="imagen_login">

        <div class="formulario-derecho">

            <h2>crear cuenta</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <input
                    type="text"
                    name="name"
                    placeholder="usuario"
                    required
                >

                <input
                    type="email"
                    name="email"
                    placeholder="correo"
                    required
                >

                <input
                    type="password"
                    name="password"
                    placeholder="contraseña"
                    required
                >

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="vuelve a escribir la contraseña"
                    required
                >

                <button type="submit">
                    crear cuenta
                </button>

                <button type="button" onclick="mostrarLogin()">
                    volver
                </button>

            </form>

        </div>

    </div>

</div>

<a href="https://github.com/jeampiervalle-dot" target="_blank">

    <img
        id="icon_gib"
        src="{{ asset('img/icongib.gif') }}"
        alt="Github"
        class="fondo_login"
    >

</a>

<div id="miPopup" class="overlay-popup">

    <div class="popup-box">

        <h3>hola bienvenido</h3>

        <p>presiona en continuar para acceder</p>

        <img
            id="img"
            src="{{ asset('img/fondo_2.jpg') }}"
            alt="Popup Image"
        >

        <button
            class="btn-cerrar"
            onclick="cerrarPopup()"
        >
            continuar
        </button>

    </div>

</div>

<img
    id="icon_gib2"
    src="{{ asset('img/giphy.gif') }}"
    alt="Gif"
    class="fondo_login"
>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    // Limpiar localStorage del toast al cargar login para resetear estado
    if (typeof(Storage) !== "undefined") {
        Object.keys(localStorage).forEach(key => {
            if (key.startsWith('taskToastVisto_')) {
                localStorage.removeItem(key);
            }
        });
    }
</script>

<div class="transicion" id="transicion"></div>
</body>
</html>

