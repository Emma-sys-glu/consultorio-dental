<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'telefono',
        'correo',
        'fecha_nacimiento',
        'curp',
        'tipo_sangre',
        'alergias',
        'antecedentes_medicos'
    ];

    public function citas()
    {
        return $this->hasMany(Cita::class);
    }

    public function expediente()
    {
        return $this->hasOne(Expediente::class);
    }
}