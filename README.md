![TaskFlow](public/img/logo.png)

# TaskFlow

**Sistema de Gestión de Tareas Personales con Notificaciones Inteligentes**

TaskFlow es una aplicación web moderna para la gestión de tareas personales, construida con **Laravel 12** y **MongoDB**. Cuenta con un sistema de notificaciones automáticas que te mantiene al tanto del estado de tus tareas: pendientes, próximas a vencer y vencidas.

---

## 🚀 Características

- ✅ **CRUD completo de tareas** — título, descripción, fecha límite, hora y prioridad
- 🔔 **Notificaciones inteligentes** — alertas automáticas por estado (pendiente, próxima a vencer, vencida) y por acciones (crear, editar, completar, eliminar)
- 👤 **Perfil de usuario** — avatar, edición de datos, cambio de contraseña y eliminación de cuenta
- 🎨 **UI moderna** — sidebar colapsable, modales, toasts, diseño responsive y glassmorphism
- 🔐 **Autenticación segura** — registro, inicio de sesión, verificación de correo y recuperación de contraseña con Laravel Breeze
- 🗄️ **MongoDB** — base de datos NoSQL schemaless, sin necesidad de migraciones
- 📱 **Responsive** — adaptado a móvil, tablet y escritorio
- 🐱 **Easter egg** — Bongo Cat interactivo al escribir en el dashboard

---

## 📋 Requisitos del sistema

| Software | Versión | Instalación |
|----------|---------|-------------|
| **PHP** | 8.2+ | `sudo apt install php8.3 php8.3-{cli,mongodb,xml,mbstring,curl,zip,gd,bcmath}` |
| **Composer** | 2.x | `php composer-setup.php --install-dir=/usr/local/bin --filename=composer` |
| **Node.js** | 20+ | `curl -fsSL https://deb.nodesource.com/setup_22.x \| bash -` |
| **npm** | 10+ | Incluido con Node.js |
| **MongoDB** | 7.0+ | `sudo apt install mongodb-org` |
| **Extensión PHP mongodb** | — | Incluida en `php8.3-mongodb` |

---

## 🛠️ Instalación

### Ubuntu / Debian (recomendado)

```bash
# 1. Clonar el repositorio
git clone https://github.com/jeampiervalle-dot/taskflow.git
cd taskflow

# 2. Ejecutar el instalador automático
chmod +x setup.sh
sudo ./setup.sh

# 3. ¡Listo! El servidor se iniciará en http://localhost:8000
```

El instalador (`setup.sh`) realiza todo automáticamente:
- Instala PHP 8.3 con todas las extensiones necesarias
- Instala Composer, Node.js 22 y npm
- Instala y configura MongoDB Community Server 7.0
- Ejecuta `composer install` y `npm install && npm run build`
- Genera el archivo `.env` y la `APP_KEY`
- Crea el enlace simbólico de storage
- Ajusta permisos

### Instalación manual

```bash
git clone https://github.com/jeampiervalle-dot/taskflow.git
cd taskflow

composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan serve
```

---

## ⚙️ Variables de entorno

Edita el archivo `.env` para configurar la aplicación:

| Variable | Descripción | Valor por defecto |
|----------|-------------|-------------------|
| `APP_NAME` | Nombre de la aplicación | `TaskFlow` |
| `APP_ENV` | Entorno (`local`, `production`) | `local` |
| `APP_DEBUG` | Modo depuración | `true` |
| `APP_URL` | URL base de la aplicación | `http://localhost:8000` |
| `DB_CONNECTION` | Conexión a base de datos | `mongodb` |
| `MONGODB_URI` | URI de conexión a MongoDB | `mongodb://127.0.0.1:27017` |
| `MONGODB_DATABASE` | Nombre de la base de datos | `taskflow` |
| `SESSION_DRIVER` | Controlador de sesión | `file` |
| `CACHE_STORE` | Almacenamiento de caché | `file` |
| `QUEUE_CONNECTION` | Conexión de colas | `sync` |
| `MAIL_MAILER` | Controlador de correo | `log` |

> **Nota:** La base de datos `taskflow` se crea automáticamente la primera vez que Laravel se conecta a MongoDB. No necesitas crear tablas manualmente.

---

## 🧪 Datos de prueba

El proyecto no incluye un seeder por defecto. Una vez registrado:

1. Crea tareas desde el dashboard con distintas fechas (hoy, mañana, hace 2 días)
2. Visita el dashboard para ver el toast con la notificación automática
3. Ve a **Notificaciones** en el sidebar para ver el listado completo, filtrar y gestionar

---

## 📁 Estructura del proyecto

```
taskflow/
├── app/
│   ├── Console/Commands/
│   │   └── SetupMongoIndexes.php      # Índices de MongoDB
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  # Autenticación (Breeze)
│   │   │   ├── TaskController.php     # CRUD de tareas + notificaciones
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   └── Models/
│       ├── User.php
│       ├── Task.php
│       └── Notification.php
├── config/                            # Configuración de Laravel
├── database/                          # Factories y seeders
├── resources/
│   ├── css/                           # Estilos (Tailwind + CSS personalizado)
│   ├── js/                            # Scripts frontend
│   └── views/                         # Plantillas Blade
│       ├── dashboard.blade.php
│       ├── home.blade.php
│       ├── notificaciones.blade.php
│       ├── auth/                      # Vistas de autenticación
│       └── profile/                   # Perfil de usuario
├── routes/
│   ├── web.php                        # Rutas de la aplicación
│   └── auth.php                       # Rutas de autenticación
├── public/
│   ├── img/                           # Imágenes, logos, gifs
│   └── build/                         # Assets compilados (Vite)
├── setup.sh                           # Instalador automático
├── ER.md                              # Diagrama entidad-relación
├── CHANGES.txt                        # Registro de cambios
└── composer.json
```

---

## 🧰 Comandos útiles

```bash
# Limpiar todas las cachés
php artisan optimize:clear

# Listar rutas
php artisan route:list

# Consola interactiva de Laravel
php artisan tinker

# Compilar assets para producción
npm run build

# Desarrollo con recarga en caliente
npm run dev

# Iniciar servidor de desarrollo
php artisan serve
```

---

## 🤝 Contribuir

1. Haz un fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Haz commit de tus cambios (`git commit -m 'Agrega nueva funcionalidad'`)
4. Haz push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la licencia **MIT**. Consulta el archivo `LICENSE` para más detalles.

---

<div align="center">
  <sub>Construido con ❤️ por Jean Pier Valle, Andree Coyla y Vanessa Coyla</sub><br>
  <sub>Curso: Base de Datos Avanzado — 2026</sub>
</div>
