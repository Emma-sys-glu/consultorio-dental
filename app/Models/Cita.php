<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cita extends Model
{
    use HasFactory;
    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'duracion_minutos',
        'motivo',
        'estado'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function dentista()
    {
        return $this->belongsTo(Dentista::class);
    }
}