# TaskFlow API (Express + MongoDB)

API REST en Node.js/Express con MongoDB (Mongoose) que replica la funcionalidad del backend Laravel original.

## Requisitos

- Node.js >= 18
- MongoDB local (`mongod`) o una URI de MongoDB Atlas

## Instalación

```bash
cd api
npm install
```

## Configuración

Copia `.env.example` a `.env` y ajusta las variables:

```
PORT=3000
NODE_ENV=development
MONGODB_URI=mongodb://127.0.0.1:27017/taskflow
JWT_SECRET=tu_clave_secreta
JWT_EXPIRES_IN=7d
CORS_ORIGIN=http://localhost:8000
```

## Ejecución

```bash
# Desarrollo (con autoreload)
npm run dev

# Producción
npm start
```

La API quedará disponible en `http://localhost:3000`.

## Endpoints

Autenticación con JWT (`Authorization: Bearer <token>`).

### Auth
- `POST /api/auth/register` — `{ name, email, password }`
- `POST /api/auth/login` — `{ email, password }`
- `POST /api/auth/logout` (protegido)
- `GET  /api/auth/me` (protegido)

### Tasks (protegidas)
- `GET    /api/tasks` — devuelve `{ tasks, notifications, nextTask, lastEditedTask, showToast }`
- `POST   /api/tasks` — `{ title, description, date, time }`
- `GET    /api/tasks/:id`
- `PUT    /api/tasks/:id` — actualiza la tarea completa
- `PATCH  /api/tasks/:id` — actualiza parcialmente (ej. `{ status: 'completed' }`)
- `DELETE /api/tasks/:id`

### Profile (protegidas)
- `GET    /api/profile`
- `PATCH  /api/profile` — `{ name?, email? }`
- `PUT    /api/profile/password` — `{ current_password, password }`
- `DELETE /api/profile` — `{ password }`

### Notifications (protegidas)
- `GET    /api/notifications`
- `PATCH  /api/notifications/:id/read`
- `DELETE /api/notifications/:id`

### Health
- `GET /api/health`

## Estructura

```
api/
├── server.js
├── package.json
├── .env.example
└── src/
    ├── config/db.js
    ├── models/{User,Task,Notification}.js
    ├── controllers/{auth,task,profile,notification}Controller.js
    ├── routes/{auth,task,profile,notification}Routes.js
    ├── middleware/{auth,errorHandler}.js
    └── utils/validators.js
```
