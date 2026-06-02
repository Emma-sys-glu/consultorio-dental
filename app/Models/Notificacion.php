<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'paciente_id',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }
}