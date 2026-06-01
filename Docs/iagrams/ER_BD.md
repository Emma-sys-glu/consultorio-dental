# Diagrama entidad-relacion de la base de datos

Este diagrama representa las tablas principales de la base de datos del sistema Consultorio Dental, sus campos y relaciones.

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string remember_token
        string rol
        timestamp created_at
        timestamp updated_at
    }

    PACIENTES {
        bigint id PK
        string nombre
        string apellido_paterno
        string apellido_materno
        string telefono
        string correo UK
        date fecha_nacimiento
        string curp
        string tipo_sangre
        string alergias
        text antecedentes_medicos
        timestamp created_at
        timestamp updated_at
    }

    DENTISTAS {
        bigint id PK
        string nombre
        string apellido_paterno
        string apellido_materno
        string especialidad
        string cedula_profesional
        string telefono
        string correo
        time horario_inicio
        time horario_fin
        string consultorio
        timestamp created_at
        timestamp updated_at
    }

    CITAS {
        bigint id PK
        bigint paciente_id FK
        bigint dentista_id FK
        date fecha
        time hora_inicio
        time hora_fin
        int duracion_minutos
        string motivo
        enum estado
        timestamp created_at
        timestamp updated_at
    }

    EXPEDIENTES {
        bigint id PK
        bigint paciente_id FK
        text diagnostico
        text observaciones
        text procedimientos_realizados
        text evolucion_tratamiento
        timestamp created_at
        timestamp updated_at
    }

    EXPEDIENTE_DOCUMENTOS {
        bigint id PK
        bigint expediente_id FK
        string tipo
        string nombre_original
        string ruta
        string mime_type
        bigint tamano
        timestamp created_at
        timestamp updated_at
    }

    TRATAMIENTOS {
        bigint id PK
        bigint paciente_id FK
        bigint dentista_id FK
        bigint expediente_id FK
        bigint cita_id FK
        string nombre
        text descripcion
        decimal costo
        enum estado
        date fecha_inicio
        date fecha_fin
        timestamp created_at
        timestamp updated_at
    }

    RECETAS {
        bigint id PK
        bigint paciente_id FK
        bigint dentista_id FK
        bigint tratamiento_id FK
        string medicamento
        string dosis
        string frecuencia
        string duracion
        text indicaciones
        date fecha_emision
        timestamp created_at
        timestamp updated_at
    }

    INVENTARIOS {
        bigint id PK
        string nombre
        string categoria
        int cantidad
        int stock_minimo
        date fecha_caducidad
        string proveedor
        decimal precio_unitario
        timestamp created_at
        timestamp updated_at
    }

    PERSONAL_ACCESS_TOKENS {
        bigint id PK
        string tokenable_type
        bigint tokenable_id
        text name
        string token UK
        text abilities
        timestamp last_used_at
        timestamp expires_at
        timestamp created_at
        timestamp updated_at
    }

    SESSIONS {
        string id PK
        bigint user_id
        string ip_address
        text user_agent
        text payload
        int last_activity
    }

    PASSWORD_RESET_TOKENS {
        string email PK
        string token
        timestamp created_at
    }

    PACIENTES ||--o{ CITAS : agenda
    DENTISTAS ||--o{ CITAS : atiende

    PACIENTES ||--o| EXPEDIENTES : tiene
    EXPEDIENTES ||--o{ EXPEDIENTE_DOCUMENTOS : almacena

    PACIENTES ||--o{ TRATAMIENTOS : recibe
    DENTISTAS ||--o{ TRATAMIENTOS : realiza
    EXPEDIENTES ||--o{ TRATAMIENTOS : registra
    CITAS ||--o{ TRATAMIENTOS : origina

    PACIENTES ||--o{ RECETAS : recibe
    DENTISTAS ||--o{ RECETAS : emite
    TRATAMIENTOS ||--o{ RECETAS : genera
```

## Relaciones principales

| Tabla origen | Tabla destino | Cardinalidad | Llave foranea |
|---|---|---|---|
| `pacientes` | `citas` | 1 a muchos | `citas.paciente_id` |
| `dentistas` | `citas` | 1 a muchos | `citas.dentista_id` |
| `pacientes` | `expedientes` | 1 a 0..1 | `expedientes.paciente_id` |
| `expedientes` | `expediente_documentos` | 1 a muchos | `expediente_documentos.expediente_id` |
| `pacientes` | `tratamientos` | 1 a muchos | `tratamientos.paciente_id` |
| `dentistas` | `tratamientos` | 1 a muchos | `tratamientos.dentista_id` |
| `expedientes` | `tratamientos` | 1 a muchos | `tratamientos.expediente_id` |
| `citas` | `tratamientos` | 1 a muchos opcional | `tratamientos.cita_id` |
| `pacientes` | `recetas` | 1 a muchos | `recetas.paciente_id` |
| `dentistas` | `recetas` | 1 a muchos | `recetas.dentista_id` |
| `tratamientos` | `recetas` | 1 a muchos opcional | `recetas.tratamiento_id` |

## Tablas de soporte de Laravel

Ademas de las tablas del negocio, Laravel crea tablas internas:

| Tabla | Uso |
|---|---|
| `users` | Usuarios que inician sesion en el sistema. |
| `sessions` | Sesiones web activas. |
| `password_reset_tokens` | Tokens para restablecer contrasenas. |
| `personal_access_tokens` | Tokens de Laravel Sanctum. |
| `cache` y `cache_locks` | Cache interna de Laravel. |
| `jobs`, `job_batches`, `failed_jobs` | Colas de trabajo de Laravel. |

## Observacion

El sistema actualmente no tiene una tabla pivote para una relacion muchos a muchos. Si necesitas demostrar ese requisito en base de datos, convendria agregar una tabla como:

```text
dentista_especialidad
- id
- dentista_id
- especialidad_id
- created_at
- updated_at
```

Esa tabla permitiria que un dentista tenga varias especialidades y que una especialidad pertenezca a varios dentistas.
