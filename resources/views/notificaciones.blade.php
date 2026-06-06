<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones · TaskFlow</title>

    <link rel="stylesheet" href="{{ asset('styles/style_noti.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="fondo-pantalla"></div>

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

            <li class="element_sidebar" onclick="window.location='{{ route('dashboard') }}'" style="cursor: pointer;">
                <i class="fa-solid fa-list-check"></i>
                <div class="sidebar_hide"><p>Tareas</p></div>
            </li>

            <li class="element_sidebar active">
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

            <li class="element_sidebar profile_item">
                <a href="{{ route('profile.edit') }}" class="logout-btn">
                    <img src="{{ asset('img/giphy.gif') }}" class="profile_img">
                    <div class="sidebar_hide">
                        <p>{{ Auth::user()->name }}</p>
                    </div>
                </a>
            </li>
        </ul>
    </aside>

    @if(session('success'))
        <div class="toast_popup" id="systemToast">
            <div class="toast_content">
                <div class="toast_header_row">
                    <strong>¡Listo!</strong>
                    <button class="toast_close_btn" onclick="closeToast('systemToast')">Cerrar</button>
                </div>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <main class="contenedor">

        <div class="notificaciones">

            <div class="noti-left">
                <img src="{{ asset('img/gif2.gif') }}" alt="Notificaciones" class="noti-icon">
            </div>

            <div class="noti-contenido">
                <h1>
                    Notificaciones
                    @if($unreadCount > 0)
                        <span class="badge-unread">{{ $unreadCount }}</span>
                    @endif
                </h1>

                <div class="menu_noti">
                    <ul>
                        <li>
                            <a href="{{ route('notificaciones.index', ['filter' => 'all']) }}" class="{{ ($filter ?? 'all') === 'all' ? 'is-active' : '' }}">
                                <i class="fa-solid fa-bell"></i> Todas <span class="count-pill">{{ $totalCount ?? $notifications->count() }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('notificaciones.index', ['filter' => 'unread']) }}" class="{{ ($filter ?? 'all') === 'unread' ? 'is-active' : '' }}">
                                <i class="fa-regular fa-envelope"></i> No leídas <span class="count-pill unread">{{ $unreadCount }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('notificaciones.index', ['filter' => 'read']) }}" class="{{ ($filter ?? 'all') === 'read' ? 'is-active' : '' }}">
                                <i class="fa-regular fa-envelope-open"></i> Leídas <span class="count-pill read">{{ $readCount ?? 0 }}</span>
                            </a>
                        </li>
                        @if($notifications->count() > 0)
                            <li>
                                <form action="{{ route('notificaciones.readAll') }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="menu-link-btn" title="Marcar todas como leídas">
                                        <i class="fa-solid fa-check-double"></i> Marcar leídas
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form action="{{ route('notificaciones.clearAll') }}" method="POST" onsubmit="return confirm('¿Eliminar todas las notificaciones? Esta acción no se puede deshacer.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="menu-link-btn" title="Eliminar todas las notificaciones">
                                        <i class="fa-solid fa-trash"></i> Limpiar todo
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>

                <ul class="noti-list">
                    @forelse($notifications as $notification)
                        <li class="noti-item {{ $notification->read ? 'read' : 'unread' }}">
                            <div class="noti-item-body">
                                <div class="noti-item-header">
                                    <strong class="noti-title">{{ $notification->title }}</strong>
                                    <small class="noti-date">
                                        {{ $notification->created_at ? $notification->created_at->diffForHumans() : '' }}
                                    </small>
                                </div>
                                <p class="noti-message">{{ $notification->message }}</p>

                                <div class="noti-actions">
                                    @if(!$notification->read)
                                        <form action="{{ route('notificaciones.read', $notification->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="noti-action-btn btn-mark-read" title="Marcar como leída">
                                                <i class="fa-regular fa-circle-check"></i> Marcar leída
                                            </button>
                                        </form>
                                    @else
                                        <span class="noti-read-label"><i class="fa-solid fa-check"></i> Leída</span>
                                    @endif

                                    <form action="{{ route('notificaciones.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta notificación?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="noti-action-btn btn-delete" title="Eliminar notificación">
                                            <i class="fa-regular fa-trash-can"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="noti-empty">
                            <i class="fa-regular fa-bell-slash"></i>
                            @if(($filter ?? 'all') === 'unread')
                                <p>No tienes notificaciones sin leer</p>
                            @elseif(($filter ?? 'all') === 'read')
                                <p>No tienes notificaciones leídas</p>
                            @else
                                <p>No tienes notificaciones todavía</p>
                            @endif
                        </li>
                    @endforelse
                </ul>
            </div>

        </div>

    </main>

    <script>
        function closeToast(id) {
            const t = document.getElementById(id);
            if (t) t.classList.add('hidden');
        }

        setTimeout(() => {
            const t = document.getElementById('systemToast');
            if (t) t.classList.add('hidden');
        }, 4000);
    </script>

</body>
</html>
