<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskFlow Dashboard</title>

    <link rel="stylesheet" href="{{ asset('styles/style2.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="min-h-screen">

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

        <li class="element_sidebar active">
            <i class="fa-solid fa-list-check"></i>
            <div class="sidebar_hide"><p>Tareas</p></div>
        </li>

        <li class="element_sidebar" onclick="window.location='{{ route('notificaciones.index') }}'" style="cursor: pointer; position: relative;">
            <i class="fa-solid fa-bell"></i>
            <div class="sidebar_hide"><p>Notificaciones</p></div>
            @if(isset($unreadCount) && $unreadCount > 0)
                <span class="sidebar-badge">{{ $unreadCount }}</span>
            @endif
        </li>

        <li class="element_sidebar" style="cursor: pointer;" 
            onclick="event.preventDefault(); localStorage.removeItem('taskToastVisto_{{ Auth::id() }}'); document.getElementById('logout-form').submit();">
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

<main class="dashboard_main">

    <section class="dashboard_header">

        <div>
            <p class="dashboard_title">Dashboard</p>
            <h1>Hola, {{ Auth::user()->name }}</h1>
        </div>

        <div class="dashboard_cards">
            
            <article class="card">
                <span class="card-label">Tareas pendientes</span>
                <strong>{{ $tasks->where('status', 'pending')->count() }}</strong>
            </article>

            <article class="card">
                <span class="card-label">Próxima tarea</span>
                @if($nextTask)
                    <strong class="next-task-text">{{ $nextTask->title }} · {{ $nextTask->date }}</strong>
                @else
                    <strong class="next-task-text empty-text">Sin tareas pendientes</strong>
                @endif
            </article>

        </div>

    </section>

    <section class="tasks_section">

        <div class="section_header">
            <h2>Tus tareas</h2>
        </div>

        <div class="task_list">

            @forelse($tasks as $task)
                <div class="task_card {{ $task->status === 'completed' ? 'task-completed-style' : '' }}">
                    <div class="task_info">
                        <h3>{{ $task->title }}</h3>
                        <p>{{ $task->description }}</p>
                        
                        <div class="task_meta_row">
                            @if($task->status === 'completed')
                                <span class="task_badge completed">Terminada</span>
                            @elseif($task->status === 'vencida' || (\Carbon\Carbon::parse($task->date . ' ' . $task->time, auth()->user()->timezone ?? config('app.timezone'))->isPast() && $task->status !== 'completed'))
                                <span class="task_badge expired" style="background-color: #e63946; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold;">Vencida</span>
                            @else
                                <span class="task_badge pending">Pendiente</span>
                            @endif
                            
                            <small class="task_date">{{ $task->date }} · {{ $task->time }}</small>
                        </div>
                    </div>

                    <div class="task_actions">
                        @if($task->status !== 'completed')
                            <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="form-check-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="task-check-action-btn" title="Marcar como terminada">
                                    <i class="fa-regular fa-circle-check"></i>
                                </button>
                            </form>

                            <button class="primary-btn btn-edit"
                                    onclick="editTask(
                                        '{{ $task->id }}',
                                        '{{ $task->title }}',
                                        `{{ $task->description }}`,
                                        '{{ $task->date }}',
                                        '{{ $task->time }}'
                                    )">
                                Editar
                            </button>
                        @endif

                        <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="primary-btn btn-delete">Eliminar</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="no-tasks">No hay tareas aún</p>
            @endforelse

        </div>

    </section>

</main>

<div class="modal_overlay hidden" id="taskModal">
    <div class="task_modal">
        <div class="modal_header">
            <h2 id="modalTitle">Crear nueva tarea</h2>
            <button class="close_modal" onclick="closeModal()">&times;</button>
        </div>

        <form id="taskForm" method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <input type="hidden" name="_method" id="methodField">

            <label>
                Título
                <input type="text" id="taskTitle" name="title" required>
            </label>

            <label>
                Descripción
                <input type="text" id="taskDescription" name="description" required>
            </label>

            <div class="form_row">
                <label>
                    Fecha
                    <input type="date" id="taskDate" name="date" required>
                </label>

                <label>
                    Hora
                    <input type="time" id="taskTime" name="time" required>
                </label>
            </div>

            <button type="submit" class="primary-btn btn-edit" style="width: 100%; justify-content: center; margin-top: 10px;">
                Guardar tarea
            </button>
        </form>
    </div>
</div>

<button class="create-task-btn" onclick="openCreate()">
    <i class="fa-solid fa-plus"></i>
</button>

<!-- TOAST RECORDATORIO: aparece SOLO en la primera visita al dashboard después del login
     y solo si existe una notificación automática (pendiente/vencida) sin leer. -->
@if(isset($showToast) && $showToast)
@php
    $autoNotif = $notifications->whereIn('type', ['pendiente', 'vencida'])->where('read', false)->sortByDesc('created_at')->first();
@endphp
@if($autoNotif)
<div class="toast_popup" id="taskToast" style="display: block;">
    <div class="toast_content">
        <div class="toast_header_row">
            <strong>
                <i class="fa-solid {{ $autoNotif->type === 'vencida' ? 'fa-circle-exclamation' : 'fa-clock' }}"
                   style="color: {{ $autoNotif->type === 'vencida' ? '#ff4757' : '#ffba08' }};"></i>
                {{ $autoNotif->title }}
            </strong>
            <a href="{{ route('notificaciones.index') }}" class="toast_close_btn" style="text-decoration: none;">Ver todas</a>
        </div>
        <p>{{ $autoNotif->message }}</p>
        <div style="display: flex; gap: 8px; margin-top: 10px;">
            <form action="{{ route('notificaciones.read', $autoNotif->id) }}" method="POST" style="display:inline; flex: 1;">
                @csrf
                @method('PATCH')
                <button type="submit" class="toast_close_btn" style="background: rgba(76, 175, 80, 0.6); width: 100%;">
                    <i class="fa-regular fa-circle-check"></i> Marcar leída
                </button>
            </form>
            <button class="toast_close_btn" onclick="closeToast('taskToast')" style="flex: 1;">
                Cerrar
            </button>
        </div>
    </div>
</div>
@endif
@endif

<div class="toast_popup hidden" id="systemToast" style="background-color: #2b2d42;">
    <div class="toast_content">
        <div class="toast_header_row">
            <strong id="systemToastTitle">Éxito</strong>
            <button class="toast_close_btn" onclick="closeToast('systemToast')">Cerrar</button>
        </div>
        <p id="systemToastMessage"></p>
    </div>
</div>

<script>
function openCreate() {
    const form = document.getElementById('taskForm');
    form.reset();
    form.action = "{{ route('tasks.store') }}";
    document.getElementById('methodField').value = "";
    document.getElementById('taskTitle').value = "";
    document.getElementById('taskDescription').value = "";
    document.getElementById('taskDate').value = "";
    document.getElementById('taskTime').value = "";
    document.getElementById('modalTitle').innerText = "Crear nueva tarea";
    document.getElementById('taskModal').classList.remove('hidden');
}

function editTask(id, title, description, date, time) {
    document.getElementById('taskTitle').value = title;
    document.getElementById('taskDescription').value = description;
    document.getElementById('taskDate').value = date;
    document.getElementById('taskTime').value = time;
    const form = document.getElementById('taskForm');
    form.action = `/tasks/${id}`;
    document.getElementById('methodField').value = "PUT";
    document.getElementById('modalTitle').innerText = "Editar tarea";
    document.getElementById('taskModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('taskModal').classList.add('hidden');
}

function closeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.style.display = 'none';
    }
}
</script>

@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
    const systemToast = document.getElementById('systemToast');
    if (systemToast) {
        document.getElementById('systemToastTitle').innerText = "¡Listo!";
        document.getElementById('systemToastMessage').innerText = "{{ session('success') }}";
        
        systemToast.classList.remove('hidden');
        
        setTimeout(() => {
            systemToast.classList.add('hidden');
        }, 4000);
    }
});
</script>
@endif

</body>
</html>