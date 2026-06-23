<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil · TaskFlow</title>

    @vite(['resources/css/app.css', 'resources/css/style2.css', 'resources/css/style_profile.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">
</head>
<body>

    <aside class="sidebar">
        <ul class="sidebar_list">
            <li class="element_sidebar element-logo">
                <div class="logo_container">
                    <i class="fa-solid fa-book-open"></i>
                    <div class="sidebar_hide">
                        <img class="logo_text" src="{{ asset('img/logo.png') }}" alt="TaskFlow">
                    </div>
                </div>
            </li>

            <li class="element_sidebar" onclick="window.location='{{ route('home') }}'">
                <i class="fa-solid fa-house"></i>
                <div class="sidebar_hide"><p>Resumen</p></div>
            </li>

            <li class="element_sidebar" onclick="window.location='{{ route('dashboard') }}'">
                <i class="fa-solid fa-list-check"></i>
                <div class="sidebar_hide"><p>Tareas</p></div>
            </li>

            <li class="element_sidebar" onclick="window.location='{{ route('notificaciones.index') }}'">
                <i class="fa-solid fa-bell"></i>
                <div class="sidebar_hide"><p>Notificaciones</p></div>
            </li>

            <li class="element_sidebar" style="cursor: pointer;"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fa-solid fa-right-from-bracket"></i>
                <div class="sidebar_hide"><p>Salir</p></div>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </li>

            <li class="element_sidebar profile_item active">
                <a href="{{ route('profile.edit') }}" class="logout-btn">
                    <img src="{{ asset('img/giphy.gif') }}" class="profile_img" loading="lazy">
                    <div class="sidebar_hide">
                        <p>{{ Auth::user()->name }}</p>
                    </div>
                </a>
            </li>
        </ul>
    </aside>

    <main class="dashboard_main">

        <section class="profile_header">
            <div class="profile_header_left">
                <p class="dashboard_title">Configuración</p>
                <h1>Mi Perfil</h1>
            </div>
            <div class="profile_avatar_block">
                <div class="profile_avatar_large">
                    {{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="profile_avatar_info">
                    <strong>{{ Auth::user()->name }}</strong>
                    <small>{{ Auth::user()->email }}</small>
                </div>
            </div>
        </section>

        @if(session('status') === 'profile-updated')
            <div class="alert alert-success" id="statusAlert">
                <i class="fa-solid fa-circle-check"></i>
                <span>Información del perfil actualizada correctamente.</span>
                <button class="alert_close" onclick="this.closest('.alert').remove()">&times;</button>
            </div>
        @endif

        @if(session('status') === 'password-updated')
            <div class="alert alert-success" id="statusAlert">
                <i class="fa-solid fa-circle-check"></i>
                <span>Contraseña actualizada correctamente.</span>
                <button class="alert_close" onclick="this.closest('.alert').remove()">&times;</button>
            </div>
        @endif

        <section class="profile_section">
            @include('profile.partials.update-profile-information-form')
        </section>

        <section class="profile_section">
            @include('profile.partials.update-password-form')
        </section>

        <section class="profile_section profile_section_danger">
            @include('profile.partials.delete-user-form')
        </section>

    </main>

    <script>
        setTimeout(() => {
            const a = document.getElementById('statusAlert');
            if (a) a.remove();
        }, 4000);
    </script>

</body>
</html>
