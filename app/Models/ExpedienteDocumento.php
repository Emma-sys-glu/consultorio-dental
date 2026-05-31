<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpedienteDocumento extends Model
{
    use HasFactory;

    protected $table = 'expediente_documentos';

    protected $fillable = [
        'expediente_id',
        'tipo',
        'nombre_original',
        'ruta',
        'mime_type',
        'tamano',
    ];

    public function expediente()
    {
        return $this->belongsTo(Expediente::class);
    }
}
