<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tratamiento extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'dentista_id',
        'expediente_id',
        'cita_id',
        'nombre',
        'descripcion',
        'costo',
        'estado',
        'fecha_inicio',
        'fecha_fin'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function dentista()
    {
        return $this->belongsTo(Dentista::class);
    }

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}