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

        <li class="element_sidebar {{ $active === 'home' ? 'active' : '' }}" onclick="window.location='{{ route('home') }}'">
            <i class="fa-solid fa-house"></i>
            <div class="sidebar_hide"><p>Resumen</p></div>
        </li>

        <li class="element_sidebar {{ $active === 'tareas' ? 'active' : '' }}" onclick="window.location='{{ route('dashboard') }}'">
            <i class="fa-solid fa-list-check"></i>
            <div class="sidebar_hide"><p>Tareas</p></div>
        </li>

        <li class="element_sidebar {{ $active === 'notificaciones' ? 'active' : '' }}" onclick="window.location='{{ route('notificaciones.index') }}'">
            <i class="fa-solid fa-bell"></i>
            <div class="sidebar_hide"><p>Notificaciones</p></div>
            @if($unreadCount > 0)
                <span class="sidebar-badge">{{ $unreadCount }}</span>
            @endif
        </li>

        @if($active === 'tareas')
        <li class="element_sidebar mobile-add-btn" onclick="openCreate()">
            <i class="fa-solid fa-plus"></i>
            <div class="sidebar_hide"><p>Crear tarea</p></div>
        </li>
        @endif

        <li class="element_sidebar"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-right-from-bracket"></i>
            <div class="sidebar_hide"><p>Salir</p></div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

        <li class="element_sidebar profile_item {{ $active === 'profile' ? 'active' : '' }}">
            <a href="{{ route('profile.edit') }}" class="logout-btn">
                <img src="{{ asset('img/giphy.gif') }}" class="profile_img" loading="lazy">
                <div class="sidebar_hide">
                    <p>{{ Auth::user()->name }}</p>
                </div>
            </a>
        </li>
    </ul>
</aside>