const TASKS_STORAGE_KEY = 'project_final_tasks';

/* CREATE TASK MODAL ELEMENTS */
const openCreateModalBtn = document.getElementById('openCreateModal');
const taskModal = document.getElementById('taskModal');
const closeTaskModalBtn = document.getElementById('closeTaskModal');
const taskForm = document.getElementById('taskForm');

const taskList = document.getElementById('taskList');
const totalCountEl = document.getElementById('totalCount');
const pendingCountEl = document.getElementById('pendingCount');
const completedCountEl = document.getElementById('completedCount');
const nextTaskEl = document.getElementById('nextTask');
const noTasksSection = document.getElementById('noTasksSection');

const editModal = document.getElementById('editTaskModal');
const closeEditModalBtn = document.getElementById('closeEditModal');
const editTaskForm = document.getElementById('editTaskForm');
const editTaskIdInput = document.getElementById('editTaskId');
const editTaskTitle = document.getElementById('editTaskTitle');
const editTaskDescription = document.getElementById('editTaskDescription');
const editTaskDate = document.getElementById('editTaskDate');
const editTaskTime = document.getElementById('editTaskTime');
const editTaskStatus = document.getElementById('editTaskStatus');
/* EDIT TASK UI: END */

function loadTasks() {
    const raw = localStorage.getItem(TASKS_STORAGE_KEY);
    if (!raw) {
        return [];
    }

    try {
        return JSON.parse(raw) || [];
    } catch (error) {
        console.error('Error parsing tasks from localStorage', error);
        return [];
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('es-ES', {
        weekday: 'short',
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
}

function formatTime(timeString) {
    if (!timeString) return '';
    const [hour, minute] = timeString.split(':').map(Number);
    if (Number.isNaN(hour) || Number.isNaN(minute)) return timeString;
    const period = hour >= 12 ? 'PM' : 'AM';
    const hour12 = (hour % 12) || 12;
    return `${hour12.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')} ${period}`;
}

function getNextTask(tasks) {
    return tasks
        .filter(task => task.status === 'pending')
        .map(task => ({
            ...task,
            dateTime: new Date(`${task.date}T${task.time}`),
        }))
        .filter(task => !Number.isNaN(task.dateTime))
        .sort((a, b) => a.dateTime - b.dateTime)[0];
}

function renderSummary(tasks) {
    const total = tasks.length;
    const pending = tasks.filter(task => task.status === 'pending').length;
    const completed = tasks.filter(task => task.status === 'completed').length;
    const nextTask = getNextTask(tasks);

    if (totalCountEl) totalCountEl.textContent = total;
    if (pendingCountEl) pendingCountEl.textContent = pending;
    if (completedCountEl) completedCountEl.textContent = completed;
    if (nextTaskEl) nextTaskEl.textContent = nextTask ? `${formatDate(nextTask.date)} · ${nextTask.title}` : 'No hay tareas próximas';
}

function renderTasks(tasks) {
    taskList.innerHTML = '';

    if (!tasks.length) {
        if (noTasksSection) noTasksSection.classList.remove('hidden');
        return;
    }

    if (noTasksSection) noTasksSection.classList.add('hidden');

    tasks
        .sort((a, b) => {
            const dateA = new Date(`${a.date}T${a.time}`);
            const dateB = new Date(`${b.date}T${b.time}`);
            return dateA - dateB;
        })
        .forEach(task => {
            const card = document.createElement('article');
            card.className = 'task_card';
            card.innerHTML = `
                <div>
                    <h3>${task.title}</h3>
                    <p>${task.description || 'Sin descripción adicional.'}</p>
                </div>
                <div class="task_meta">
                    <div class="task_tags">
                        <span class="task_badge ${task.status}">${task.status === 'pending' ? 'Pendiente' : 'Completada'}</span>
                        <span>${formatDate(task.date)}</span>
                        <span>${formatTime(task.time)}</span>
                    </div>
                    <div class="task_actions">
                        <button class="primary-btn edit-task-btn" data-id="${task.id}">Editar</button>
                        <button class="secondary-btn toggle-status-btn" data-id="${task.id}">${task.status === 'pending' ? 'Marcar completada' : 'Marcar pendiente'}</button>
                        <button class="secondary-btn delete-task-btn" data-id="${task.id}">Eliminar</button>
                    </div>
                </div>
            `;
            taskList.appendChild(card);
        });

    document.querySelectorAll('.toggle-status-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            toggleStatus(id);
        });
    });

    document.querySelectorAll('.delete-task-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            deleteTask(id);
        });
    });

    document.querySelectorAll('.edit-task-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            openEditModal(id);
        });
    });
}

function saveTasks(tasks) {
    localStorage.setItem(TASKS_STORAGE_KEY, JSON.stringify(tasks));
}

function toggleStatus(id) {
    const tasks = loadTasks().map(task => {
        if (task.id === id) {
            return {
                ...task,
                status: task.status === 'pending' ? 'completed' : 'pending',
            };
        }
        return task;
    });

    saveTasks(tasks);
    renderSummary(tasks);
    renderTasks(tasks);
}

function deleteTask(id) {
    const tasks = loadTasks().filter(task => task.id !== id);
    saveTasks(tasks);
    renderSummary(tasks);
    renderTasks(tasks);
}

function openCreateModal() {
    if (taskModal) taskModal.classList.remove('hidden');
}

function closeCreateModal() {
    if (taskModal) taskModal.classList.add('hidden');
    if (taskForm) taskForm.reset();
    const today = new Date().toISOString().slice(0, 10);
    const dateInput = document.getElementById('taskDate');
    if (dateInput) dateInput.value = today;
}

function initPage() {
    const tasks = loadTasks();
    renderSummary(tasks);
    renderTasks(tasks);
    
    // Setup create task modal listeners
    if (openCreateModalBtn) {
        openCreateModalBtn.addEventListener('click', openCreateModal);
    }
    if (closeTaskModalBtn) {
        closeTaskModalBtn.addEventListener('click', closeCreateModal);
    }
    
    // Handle create task form submit
    if (taskForm) {
        taskForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const title = document.getElementById('taskTitle')?.value.trim();
            const description = document.getElementById('taskDescription')?.value.trim();
            const date = document.getElementById('taskDate')?.value;
            const time = document.getElementById('taskTime')?.value;
            const status = document.getElementById('taskStatus')?.value || 'pending';
            
            if (!title || !date || !time) {
                alert('Completa el título, fecha y hora.');
                return;
            }
            
            const newTask = {
                id: crypto.randomUUID ? crypto.randomUUID() : Date.now().toString(),
                title,
                description,
                date,
                time,
                status,
            };
            
            const allTasks = loadTasks();
            allTasks.push(newTask);
            saveTasks(allTasks);
            renderSummary(allTasks);
            renderTasks(allTasks);
            closeCreateModal();
        });
    }
    
    // Setup date input default value
    const taskDateInput = document.getElementById('taskDate');
    if (taskDateInput) {
        const today = new Date().toISOString().slice(0, 10);
        taskDateInput.value = today;
        taskDateInput.min = today;
    }
    
    // modal handlers (guarded)
    if (closeEditModalBtn) {
        closeEditModalBtn.addEventListener('click', () => {
            if (editModal) {
                editModal.classList.add('hidden');
                try { editModal.setAttribute('hidden', ''); } catch (e) {}
            }
            if (editTaskForm) editTaskForm.reset();
        });
    }

    if (editTaskForm) {
        editTaskForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const id = editTaskIdInput?.value;
            if (!id) return;
            const tasks = loadTasks().map(t => {
                if (t.id === id) {
                    return {
                        ...t,
                        title: editTaskTitle?.value.trim() || '',
                        description: editTaskDescription?.value.trim() || '',
                        date: editTaskDate?.value || '',
                        time: editTaskTime?.value || '',
                        status: editTaskStatus?.value || 'pending',
                    };
                }
                return t;
            });
            saveTasks(tasks);
            renderSummary(tasks);
            renderTasks(tasks);
            if (editModal) {
                editModal.classList.add('hidden');
                try { editModal.setAttribute('hidden', ''); } catch (e) {}
            }
        });
    }
}

function openEditModal(id) {
    const tasks = loadTasks();
    const task = tasks.find(t => t.id === id);
    if (!task) return;
    editTaskIdInput.value = task.id;
    editTaskTitle.value = task.title || '';
    editTaskDescription.value = task.description || '';
    editTaskDate.value = task.date || '';
    editTaskTime.value = task.time || '';
    editTaskStatus.value = task.status || 'pending';
    if (editModal) {
        editModal.classList.remove('hidden');
        try { editModal.removeAttribute('hidden'); } catch (e) {}
    }
    /* NOTE: To customize opening behavior (animations, focus, etc.), edit
       this function. Keep the ID references in sync with the HTML if you
       rename inputs. */
}

window.addEventListener('DOMContentLoaded', initPage);
