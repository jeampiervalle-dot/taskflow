<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow - Login</title>

    @vite(['resources/css/style.css', 'resources/js/app.js', 'resources/js/script.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>

<img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo" loading="lazy">

<div class="contenedor-login">

   @php
    $showRegister = request()->boolean('register') 
        || $errors->has('name') 
        || $errors->has('email') 
        || $errors->has('password');
@endphp
    <!-- LOGIN -->
    <div class="login {{ $showRegister ? 'hidden-left' : 'active-panel' }}" id="loginPanel">

        <img src="{{ asset('img/icono pantalla.png') }}" alt="Login Art" class="imagen_login">

        <div class="formulario-derecho">

            <h2>iniciar sesion</h2>
            @if ($errors->any() && !$errors->has('name') && !$errors->has('password') && !$errors->has('email'))
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
                    value="{{ old('email') }}"
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
    <div class="login {{ $showRegister ? 'active-panel' : 'hidden-right' }}" id="registerPanel">

        <img src="{{ asset('img/fondo 3.png') }}" alt="Register Art" class="imagen_login">

        <div class="formulario-derecho">

            <h2>crear cuenta</h2>

            @if ($errors->any() && ($errors->has('name') || $errors->has('email') || $errors->has('password')))
    <div class="error-login">

        @if ($errors->has('name'))
            <div>El nombre es obligatorio</div>
        @endif

        @if ($errors->has('email'))
            @if ($errors->first('email') == 'The email has already been taken.')
                <div>El correo ya está registrado</div>
            @elseif ($errors->first('email') == 'The email field is required.')
                <div>El correo es obligatorio</div>
            @else
                <div>El correo no es válido</div>
            @endif
        @endif

        @if ($errors->has('password'))
            @if ($errors->first('password') == 'The password field is required.')
                <div>La contraseña es obligatoria</div>
            @elseif ($errors->first('password') == 'The password confirmation does not match.')
                <div>Las contraseñas no coinciden</div>
            @else
                <div>Error en la contraseña</div>
            @endif
        @endif

    </div>
@endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <input
                    type="text"
                    name="name"
                    placeholder="usuario"
                    value="{{ old('name') }}"
                    required
                >

                <input
                    type="email"
                    name="email"
                    placeholder="correo"
                    value="{{ old('email') }}"
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

<footer class="login_footer">
    <div class="footer_icons">
        <a href="https://github.com/jeampiervalle-dot" target="_blank">
            <img src="{{ asset('img/icongib.gif') }}" alt="Github" loading="lazy">
        </a>
        <img src="{{ asset('img/giphy.gif') }}" alt="TaskFlow" loading="lazy">
    </div>
    <div class="footer_copy">
        &copy; {{ date('Y') }} TaskFlow · Built by Jean Pierre Valle
    </div>
</footer>

<div id="miPopup" class="overlay-popup" onclick="this.style.display='none'">

    <div class="popup-box">

        <span class="popup-text">bienvenido</span>

        <span class="popup-hint">presiona Enter para continuar</span>

    </div>

</div>

<script>
    document.addEventListener('keydown', function handler(e) {
        if (e.key === 'Enter') {
            const popup = document.getElementById('miPopup');
            if (popup && popup.style.display !== 'none') {
                cerrarPopup();
            }
        }
    });

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

