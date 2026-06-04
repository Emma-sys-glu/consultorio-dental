<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria',
        'cantidad',
        'stock_c1',
        'stock_c2',
        'stock_c3',
        'stock_c4',
        'stock_minimo',
        'fecha_caducidad',
        'proveedor',
        'precio_unitario'
    ];
}