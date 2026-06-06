# TaskFlow

Aplicación web de gestión de tareas con sistema de notificaciones inteligente, construida con **Laravel 11 + MongoDB**.

---

## ✨ Características

- ✅ CRUD completo de tareas con título, descripción, fecha límite y prioridad
- 🔔 **Sistema de notificaciones automático** basado en el estado de cada tarea (`pendiente`, `próxima a vencer`, `vencida`)
- 👤 Perfil de usuario con avatar, edición de datos, cambio de contraseña y eliminación de cuenta
- 🎨 UI con sidebar de navegación, glassmorphism y diseño responsive
- 🔐 Autenticación completa con Laravel Breeze
- 🗄️ **MongoDB** como base de datos (no requiere migraciones SQL)

---

## 📋 Requisitos previos

Antes de instalar, asegúrate de tener:

| Software       | Versión     | Descarga                                                                 |
|----------------|-------------|--------------------------------------------------------------------------|
| **PHP**        | 8.2 o superior | https://windows.php.net/download/                                    |
| **Composer**   | 2.x         | https://getcomposer.org/Composer-Setup.exe                              |
| **Node.js**    | 18 o superior | https://nodejs.org/                                                   |
| **MongoDB**    | 6 o superior | https://www.mongodb.com/try/download/community                        |
| **Extensión PHP `mongodb`** | — | https://pecl.php.net/package/mongodb                          |

> 💡 El proyecto trae un `setup.ps1` que valida todo esto automáticamente.

---

## 🚀 Instalación rápida (Windows)

### Opción 1 — Script automático (recomendado)

Una vez clonado el repo, dentro de la carpeta del proyecto:

```powershell
# 1. Clonar
git clone https://github.com/TU_USUARIO/taskflow.git
cd taskflow

# 2. Ejecutar el instalador (valida requisitos, instala todo, genera APP_KEY)
powershell -ExecutionPolicy Bypass -File .\setup.ps1

# 3. Arrancar
php artisan serve
```

Abre http://localhost:8000

### Opción 2 — Manual

```powershell
# 1. Clonar
git clone https://github.com/TU_USUARIO/taskflow.git
cd taskflow

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias JS
npm install

# 4. Crear archivo de entorno
copy .env.example .env

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Crear enlace simbólico de storage
php artisan storage:link

# 7. (Opcional) Compilar assets
npm run dev

# 8. Arrancar servidor
php artisan serve
```

> ⚠️ **Importante:** Asegúrate de que el servicio de MongoDB esté corriendo antes de abrir la app. Si lo instalaste como servicio de Windows, ya inicia automáticamente. Si no: `mongod --dbpath C:\data\db`

---

## ⚙️ Configuración de MongoDB

Edita `.env` si tu MongoDB está en otra ubicación:

```env
DB_CONNECTION=mongodb
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=taskflow
```

La base de datos `taskflow` se crea automáticamente la primera vez que Laravel se conecta; no necesitas crear tablas manualmente.

---

## 🧪 Datos de prueba

El proyecto no incluye un seeder por defecto. Una vez que te registres, puedes:

1. Crear tareas desde la UI con distintas fechas (hoy, mañana, hace 2 días)
2. Visitar el dashboard para ver el toast con la notificación automática
3. Ir a **Notificaciones** en el sidebar para ver el listado completo, filtrar y gestionar

---

## 📁 Estructura del proyecto

```
taskflow/
├── app/
│   ├── Http/Controllers/
│   │   ├── NotificationController.php    # CRUD de notificaciones
│   │   ├── TaskController.php            # Tareas + sync de notificaciones
│   │   └── ...
│   └── Models/
│       ├── Notification.php              # task_id, type, read, user_id
│       └── Task.php                      # notification_dismissed_at, last_state_notified
├── resources/
│   └── views/
│       ├── dashboard.blade.php           # Dashboard con sidebar + toast
│       ├── notificaciones.blade.php      # Página de notificaciones
│       └── profile/                      # Edición de perfil
├── public/
│   └── styles/
│       ├── style2.css                    # Dashboard
│       ├── style_noti.css                # Notificaciones
│       └── style_profile.css             # Perfil
├── routes/web.php                        # Rutas de la app
├── setup.ps1                             # Script de instalación
└── .env.example                          # Plantilla de variables de entorno
```

---


## 🛠️ Comandos útiles

```powershell
# Limpiar todos los caches
php artisan optimize:clear

# Ver rutas
php artisan route:list

# Consola interactiva de Laravel
php artisan tinker

# Compilar assets para producción
npm run build
```

---

## 📜 Licencia

MIT
