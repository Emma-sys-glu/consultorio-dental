<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expediente extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'diagnostico',
        'observaciones',
        'procedimientos_realizados',
        'evolucion_tratamiento'
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function documentos()
    {
        return $this->hasMany(ExpedienteDocumento::class);
    }
}
