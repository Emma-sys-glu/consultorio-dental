<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receta extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'tratamiento_id',
        'medicamento',
        'dosis',
        'frecuencia',
        'duracion',
        'indicaciones',
        'fecha_emision'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function dentista()
    {
        return $this->belongsTo(Dentista::class);
    }

    public function tratamiento()
    {
        return $this->belongsTo(Tratamiento::class);
    }
}