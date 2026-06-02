<?php

namespace App\Jobs;

use App\Models\Inventario;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RevisarInventarioJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        $productosStockBajo = Inventario::whereColumn(
            'cantidad',
            '<=',
            'stock_minimo'
        )->get();

        foreach ($productosStockBajo as $producto) {
            Log::warning(
                'ALERTA STOCK BAJO: ' .
                $producto->nombre .
                ' - Cantidad actual: ' .
                $producto->cantidad .
                ' - Stock mínimo: ' .
                $producto->stock_minimo
            );
        }

        $productosCaducar = Inventario::whereNotNull('fecha_caducidad')
            ->whereDate(
                'fecha_caducidad',
                '<=',
                Carbon::now()->addDays(30)
            )
            ->get();

        foreach ($productosCaducar as $producto) {
            Log::warning(
                'ALERTA CADUCIDAD: ' .
                $producto->nombre .
                ' vence el ' .
                $producto->fecha_caducidad
            );
        }
    }
}