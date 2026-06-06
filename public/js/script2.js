const STORAGE_TASKS_KEY = 'project_final_tasks';
const STORAGE_USER_KEY = 'project_final_user';

let toastTimeout = null;
let editingTaskId = null;

/* =========================
   ELEMENTOS
========================= */

const btnSalir = document.getElementById('btn-salir');
const sidebarTareas = document.getElementById('sidebar-tareas');
const createTaskBtn = document.querySelector('.create-task-btn');
const taskModal = document.getElementById('taskModal');
const taskForm = document.getElementById('taskForm');
const taskList = document.getElementById('taskList');
const pendingCountEl = document.getElementById('pendingCount');
const nextTaskTitleEl = document.getElementById('nextTaskTitle');

/* =========================
   LOCAL STORAGE (SOLO SI LO USAS)
========================= */

function saveTasks(tasks) {
    localStorage.setItem(STORAGE_TASKS_KEY, JSON.stringify(tasks));
}

function loadTasks() {
    const raw = localStorage.getItem(STORAGE_TASKS_KEY);
    if (!raw) return [];
    try {
        return JSON.parse(raw) || [];
    } catch {
        return [];
    }
}

/* =========================
   MODAL CONTROL
========================= */

function openModal() {
    taskModal.classList.remove('hidden');
}

function closeModal() {
    taskModal.classList.add('hidden');
    taskForm.reset();

    const methodInput = document.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();

    taskForm.action = "{{ route('tasks.store') }}";
    editingTaskId = null;
}

/* =========================
   CREAR
========================= */

function openCreate() {
    taskForm.reset();

    taskForm.action = "{{ route('tasks.store') }}";

    const methodInput = document.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();

    editingTaskId = null;

    document.getElementById('modalTitle').innerText = "Crear nueva tarea";

    openModal();
}

/* =========================
   EDITAR (LARAVEL)
========================= */

function editTask(id) {

    fetch(`/tasks/${id}/edit`)
        .then(res => res.json())
        .then(data => {

            document.getElementById('taskTitle').value = data.title || '';
            document.getElementById('taskDescription').value = data.description || '';
            document.getElementById('taskDate').value = data.date || '';
            document.getElementById('taskTime').value = data.time || '';

            taskForm.action = `/tasks/${id}`;

            let methodInput = document.querySelector('input[name="_method"]');

            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                taskForm.appendChild(methodInput);
            }

            methodInput.value = 'PUT';

            editingTaskId = id;

            document.getElementById('modalTitle').innerText = "Editar tarea";

            openModal();
        });
}

/* =========================
   TOAST
========================= */

function showToast(title, message, time = 4000) {
    const toast = document.getElementById('taskToast');

    document.getElementById('toastTitle').innerText = title;
    document.getElementById('toastMessage').innerText = message;

    toast.classList.remove('hidden');

    clearTimeout(toastTimeout);

    toastTimeout = setTimeout(() => {
        closeToast();
    }, time);
}

function closeToast() {
    document.getElementById('taskToast').classList.add('hidden');
}

/* =========================
   LOGOUT
========================= */

document.addEventListener("DOMContentLoaded", () => {

    const btnSalir = document.getElementById("btn-salir");

    if (btnSalir) {
        btnSalir.addEventListener("click", () => {
            document.getElementById("logout-form")?.submit();
        });
    }

    // botón crear
    if (createTaskBtn) {
        createTaskBtn.addEventListener('click', openCreate);
    }

    // cerrar modal si existe botón
    document.querySelectorAll('.close_modal').forEach(btn => {
        btn.addEventListener('click', closeModal);
    });

});