# Diagrama Entidad-Relación — TaskFlow

Proyecto Laravel + MongoDB con 3 colecciones: `users`, `tasks`, `notifications`.

## Diagrama ER (Mermaid)

```mermaid
erDiagram
    USER ||--o{ TASK : crea
    USER ||--o{ NOTIFICATION : recibe

    USER {
        ObjectId _id PK
        string name
        string email UK
        string password
        datetime email_verified_at
        datetime created_at
        datetime updated_at
    }

    TASK {
        ObjectId _id PK
        ObjectId user_id FK
        string title
        string description
        string fecha
        string hora
        string status
        datetime created_at
        datetime updated_at
    }

    NOTIFICATION {
        ObjectId _id PK
        ObjectId user_id FK
        string title
        string message
        boolean leido
        datetime created_at
        datetime updated_at
    }
```

## Diagrama de clases (Mermaid)

```mermaid
classDiagram
    direction LR

    class User {
        +ObjectId _id
        +string name
        +string email
        +string password
        +tasks() HasMany
    }

    class Task {
        +ObjectId _id
        +ObjectId user_id
        +string title
        +string description
        +string date
        +string time
        +string status
        +user() BelongsTo
    }

    class Notification {
        +ObjectId _id
        +ObjectId user_id
        +string title
        +string message
        +bool read
    }

    User "1" --> "0..*" Task : hasMany
    Task "0..*" --> "1" User : belongsTo
    User "1" --> "0..*" Notification : user_id
```

## Versión PlantUML (si usas @startuml)

```plantuml
@startuml TaskFlow_ER
!define ENTITY(name) entity name
hide circle
skinparam linetype ortho

entity "User" as user {
  * _id : ObjectId <<PK>>
  --
  name : string
  email : string <<UK>>
  password : string
  email_verified_at : datetime
  created_at : datetime
  updated_at : datetime
}

entity "Task" as task {
  * _id : ObjectId <<PK>>
  --
  user_id : ObjectId <<FK>>
  title : string
  description : string
  date : string
  time : string
  status : string
  created_at : datetime
  updated_at : datetime
}

entity "Notification" as notif {
  * _id : ObjectId <<PK>>
  --
  user_id : ObjectId <<FK>>
  title : string
  message : string
  read : boolean
  created_at : datetime
  updated_at : datetime
}

user ||--o{ task : "crea"
user ||--o{ notif : "recibe"
@enduml
```

## Cardinalidades

| Relación | Cardinalidad | Descripción |
|----------|--------------|-------------|
| User → Task | 1 : N | Un usuario crea muchas tareas |
| User → Notification | 1 : N | Un usuario recibe muchas notificaciones |

## Dónde renderizarlo

| Formato | Herramienta |
|---------|-------------|
| Mermaid | https://mermaid.live · VS Code (ext. *Markdown Preview Mermaid Support*) · GitHub |
| PlantUML | https://www.plantuml.com/plantuml/uml · VS Code (ext. *PlantUML* de jebbs) |
