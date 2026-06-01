# Diagrama UML de clases - Consultorio Dental

Este diagrama representa las clases principales del sistema y sus relaciones Eloquent.

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string email
        +string password
        +string rol
        +datetime email_verified_at
        +string remember_token
    }

    class Paciente {
        +int id
        +string nombre
        +string apellido_paterno
        +string apellido_materno
        +string telefono
        +string correo
        +date fecha_nacimiento
        +string curp
        +string tipo_sangre
        +text alergias
        +text antecedentes_medicos
        +citas()
        +expediente()
    }

    class Dentista {
        +int id
        +string nombre
        +string apellido_paterno
        +string apellido_materno
        +string especialidad
        +string cedula_profesional
        +string telefono
        +string correo
        +time horario_inicio
        +time horario_fin
        +string consultorio
        +citas()
    }

    class Cita {
        +int id
        +int paciente_id
        +int dentista_id
        +date fecha
        +time hora_inicio
        +time hora_fin
        +int duracion_minutos
        +string motivo
        +string estado
        +paciente()
        +dentista()
    }

    class Expediente {
        +int id
        +int paciente_id
        +text diagnostico
        +text observaciones
        +text procedimientos_realizados
        +text evolucion_tratamiento
        +paciente()
        +documentos()
    }

    class ExpedienteDocumento {
        +int id
        +int expediente_id
        +string tipo
        +string nombre_original
        +string ruta
        +string mime_type
        +int tamano
        +expediente()
    }

    class Tratamiento {
        +int id
        +int paciente_id
        +int dentista_id
        +int expediente_id
        +int cita_id
        +string nombre
        +text descripcion
        +decimal costo
        +string estado
        +date fecha_inicio
        +date fecha_fin
        +paciente()
        +dentista()
        +expediente()
        +cita()
    }

    class Receta {
        +int id
        +int paciente_id
        +int dentista_id
        +int tratamiento_id
        +string medicamento
        +string dosis
        +string frecuencia
        +string duracion
        +text indicaciones
        +date fecha_emision
        +paciente()
        +dentista()
        +tratamiento()
    }

    class Inventario {
        +int id
        +string nombre
        +string categoria
        +int cantidad
        +int stock_minimo
        +date fecha_caducidad
        +string proveedor
        +decimal precio_unitario
    }

    Paciente "1" --> "0..*" Cita : tiene
    Dentista "1" --> "0..*" Cita : atiende

    Paciente "1" --> "0..1" Expediente : posee
    Expediente "1" --> "0..*" ExpedienteDocumento : contiene

    Paciente "1" --> "0..*" Tratamiento : recibe
    Dentista "1" --> "0..*" Tratamiento : realiza
    Expediente "1" --> "0..*" Tratamiento : registra
    Cita "0..1" --> "0..*" Tratamiento : origina

    Paciente "1" --> "0..*" Receta : recibe
    Dentista "1" --> "0..*" Receta : emite
    Tratamiento "0..1" --> "0..*" Receta : genera
```

## Relaciones principales

| Relacion | Tipo | En Laravel |
|---|---|---|
| Paciente - Expediente | Uno a uno | `Paciente::hasOne(Expediente::class)` |
| Paciente - Cita | Uno a muchos | `Paciente::hasMany(Cita::class)` |
| Dentista - Cita | Uno a muchos | `Dentista::hasMany(Cita::class)` |
| Cita - Paciente | Muchos a uno | `Cita::belongsTo(Paciente::class)` |
| Cita - Dentista | Muchos a uno | `Cita::belongsTo(Dentista::class)` |
| Expediente - Documento | Uno a muchos | `Expediente::hasMany(ExpedienteDocumento::class)` |
| Tratamiento - Paciente | Muchos a uno | `Tratamiento::belongsTo(Paciente::class)` |
| Tratamiento - Dentista | Muchos a uno | `Tratamiento::belongsTo(Dentista::class)` |
| Tratamiento - Expediente | Muchos a uno | `Tratamiento::belongsTo(Expediente::class)` |
| Tratamiento - Cita | Muchos a uno | `Tratamiento::belongsTo(Cita::class)` |
| Receta - Paciente | Muchos a uno | `Receta::belongsTo(Paciente::class)` |
| Receta - Dentista | Muchos a uno | `Receta::belongsTo(Dentista::class)` |
| Receta - Tratamiento | Muchos a uno | `Receta::belongsTo(Tratamiento::class)` |

## Nota sobre muchos a muchos

Actualmente el proyecto no tiene una relacion muchos a muchos implementada con `belongsToMany`.

Una opcion recomendable seria agregar:

- `Dentista` muchos a muchos `Especialidad`
- `Paciente` muchos a muchos `Alergia`
- `Tratamiento` muchos a muchos `Insumo`

Ejemplo conceptual:

```php
public function especialidades()
{
    return $this->belongsToMany(Especialidad::class);
}
```

Con eso el proyecto podria demostrar los tres tipos principales de relaciones ORM: uno a uno, uno a muchos y muchos a muchos.
