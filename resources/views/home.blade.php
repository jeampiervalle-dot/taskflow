<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen · TaskFlow</title>

    @vite(['resources/css/style2.css', 'resources/css/style_home.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" media="print" onload="this.media='all'">
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

        <li class="element_sidebar active">
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
            @if($unreadCount > 0)
                <span class="sidebar-badge">{{ $unreadCount }}</span>
            @endif
        </li>

        <li class="element_sidebar"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa-solid fa-right-from-bracket"></i>
            <div class="sidebar_hide"><p>Salir</p></div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>

        <li class="element_sidebar profile_item">
            <a href="{{ route('profile.edit') }}" class="logout-btn">
                <img src="{{ asset('img/giphy.gif') }}" class="profile_img" loading="lazy">
                <div class="sidebar_hide">
                    <p>{{ Auth::user()->name }}</p>
                </div>
            </a>
        </li>
    </ul>
</aside>

<main class="dashboard_main home_main">

    <div class="home_header">
        <div>
            <div class="dashboard_title">Resumen general</div>
            <h1>Hola, {{ Auth::user()->name }}</h1>
        </div>
    </div>

    <div class="home_grid">

        <!-- Círculo de progreso -->
        <div class="home_card progress_card">
            <div class="progress_circle_wrap">
                <svg class="progress_circle" viewBox="0 0 180 180">
                    <circle class="progress_bg" cx="90" cy="90" r="78" />
                    <circle class="progress_fill" cx="90" cy="90" r="78"
                        style="stroke-dasharray: 490.09; stroke-dashoffset: {{ 490.09 - (490.09 * $percent / 100) }};" />
                </svg>
                <div class="progress_center">
                    <span class="progress_percent">{{ $percent }}%</span>
                    <span class="progress_label">completado</span>
                </div>
            </div>
            <div class="progress_info">
                <span>{{ $completed }}/{{ $total }} tareas</span>
            </div>
        </div>

        <!-- Stats rápidas -->
        <div class="home_card stats_card">
            <h3><i class="fa-solid fa-chart-simple"></i> Estadísticas</h3>
            <div class="stats_grid">
                <div class="stat_item">
                    <span class="stat_num total">{{ $total }}</span>
                    <span class="stat_label">Totales</span>
                </div>
                <div class="stat_item">
                    <span class="stat_num pending">{{ $pending }}</span>
                    <span class="stat_label">Pendientes</span>
                </div>
                <div class="stat_item">
                    <span class="stat_num completed">{{ $completed }}</span>
                    <span class="stat_label">Completadas</span>
                </div>
                <div class="stat_item">
                    <span class="stat_num expired">{{ $expired }}</span>
                    <span class="stat_label">Vencidas</span>
                </div>
            </div>
        </div>

        <!-- Próxima tarea -->
        <div class="home_card next_card">
            <h3><i class="fa-solid fa-calendar-day"></i> Próxima tarea</h3>
            @if($nextTask)
                <div class="next_task_info">
                    <strong>{{ $nextTask->title }}</strong>
                    <span>{{ $nextTask->date }} · {{ $nextTask->time }}</span>
                </div>
                <button class="home_btn" onclick="window.location='{{ route('dashboard') }}'">Ir a tareas</button>
            @else
                <div class="next_task_empty">
                    <i class="fa-regular fa-face-smile"></i>
                    <p>No hay tareas pendientes</p>
                </div>
            @endif
        </div>

        <!-- Cómo mejorar -->
        <div class="home_card improve_card">
            <h3><i class="fa-solid fa-lightbulb"></i> Cómo mejorar</h3>
            <ul class="improve_list">
                @if($percent < 30)
                    <li><i class="fa-solid fa-flag"></i> Empieza completando tareas pequeñas para ganar ritmo</li>
                    <li><i class="fa-solid fa-list"></i> Prioriza las tareas con fecha más próxima</li>
                    <li><i class="fa-solid fa-clock"></i> Dedica 15 minutos al día a organizarte</li>
                @elseif($percent < 70)
                    <li><i class="fa-solid fa-arrow-up"></i> ¡Buen progreso! Sigue así</li>
                    <li><i class="fa-solid fa-calendar-check"></i> Revisa las tareas vencidas para retomarlas</li>
                    <li><i class="fa-solid fa-star"></i> Intenta completar al menos una tarea diaria</li>
                @else
                    <li><i class="fa-solid fa-trophy"></i> Excelente rendimiento</li>
                    <li><i class="fa-solid fa-rocket"></i> Mantén el ritmo para llegar al 100%</li>
                    <li><i class="fa-solid fa-medal"></i> Revisa tareas completadas y celebra tus logros</li>
                @endif
            </ul>
        </div>

        <!-- Almanaque -->
        <script>
            window._calTareas = @json($allTasksJson);
            window._calApp = function() {
                return {
                    selDay: null,
                    month: {{ $calMonth }},
                    year: {{ $calYear }},
                    monthName: '{{ $calMonthName }}',
                    todas: window._calTareas,
                    meses: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                    get tareasMes() {
                        const g = {};
                        this.todas.forEach(t => {
                            if (!t.date) return;
                            const p = t.date.split('-');
                            const y = +p[0], m = +p[1], d = +p[2];
                            if (y === this.year && m === this.month) {
                                if (!g[d]) g[d] = [];
                                g[d].push(t);
                            }
                        });
                        return g;
                    },
                    get diasMes() { return new Date(this.year, this.month, 0).getDate(); },
                    get primerDow() { return new Date(this.year, this.month - 1, 1).getDay(); },
                    navegar(dir) {
                        this.month += dir;
                        if (this.month < 1) { this.month = 12; this.year--; }
                        if (this.month > 12) { this.month = 1; this.year++; }
                        this.monthName = this.meses[this.month - 1];
                        this.selDay = null;
                    },
                    vacios() { return Array(this.primerDow).fill(0); },
                    diaHoy() {
                        const h = new Date();
                        if (h.getFullYear() === this.year && h.getMonth() + 1 === this.month) return h.getDate();
                        return null;
                    },
                    dias() {
                        const t = this.tareasMes;
                        const hoy = this.diaHoy;
                        const r = [];
                        for (let d = 1; d <= this.diasMes; d++) {
                            const h = t[d] || [];
                            r.push({ n: d, esHoy: d === hoy, pend: h.some(x => x.status !== 'completed'), done: h.some(x => x.status === 'completed') });
                        }
                        return r;
                    }
                };
            };
        </script>
        <div class="home_card calendar_card" x-data="window._calApp()">
            <div class="cal_header">
                <button class="cal_nav" @click="navegar(-1)" aria-label="Anterior">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <h3><i class="fa-solid fa-calendar-alt"></i> <span x-text="monthName"></span> <span x-text="year"></span></h3>
                <button class="cal_nav" @click="navegar(1)" aria-label="Siguiente">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            <div class="cal_grid">
                <span class="cal_dow">Dom</span> <span class="cal_dow">Lun</span> <span class="cal_dow">Mar</span>
                <span class="cal_dow">Mié</span> <span class="cal_dow">Jue</span> <span class="cal_dow">Vie</span> <span class="cal_dow">Sáb</span>
                <template x-for="(_, i) in vacios()" :key="'e'+i">
                    <span class="cal_day cal_empty"></span>
                </template>
                <template x-for="d in dias()" :key="d.n">
                    <span class="cal_day"
                        :class="{ cal_today: d.esHoy, cal_has_tasks: d.pend || d.done, cal_has_pending: d.pend }"
                        @click="selDay = selDay === d.n ? null : d.n">
                        <span x-text="d.n"></span>
                        <span class="cal_dots" x-show="d.pend || d.done">
                            <span class="cal_dot cal_dot_pending" x-show="d.pend"></span>
                            <span class="cal_dot cal_dot_done" x-show="d.done"></span>
                        </span>
                    </span>
                </template>
            </div>
            <div class="cal_detail" x-show="selDay && tareasMes[selDay]" x-cloak>
                <div class="cal_detail_header" x-text="monthName + ' ' + selDay"></div>
                <template x-for="t in tareasMes[selDay]" :key="t.title + (t.time || '')">
                    <div class="cal_detail_item"
                        :class="t.status === 'completed' ? 'tt_done' : (t.status === 'vencida' ? 'tt_expired' : '')">
                        <span class="tt_bullet"></span>
                        <span class="tt_title" x-text="t.title"></span>
                        <span class="tt_time" x-text="t.time" x-show="t.time"></span>
                    </div>
                </template>
            </div>
            <div class="cal_empty_state" x-show="!selDay">Selecciona un día para ver sus tareas</div>
            <div class="cal_empty_state cal_no_tasks" x-show="selDay && !tareasMes[selDay]" x-cloak>Sin tareas este día</div>
        </div>

    </div>

</main>

</body>
</html>
