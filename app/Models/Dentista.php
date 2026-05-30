<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Dentista extends Model
{

 use HasFactory;
    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'especialidad',
        'cedula_profesional',
        'telefono',
        'correo',
        'horario_inicio',
        'horario_fin',
        'consultorio'
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }
}