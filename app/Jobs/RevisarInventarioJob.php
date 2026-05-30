<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\Inventario;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RevisarInventarioJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
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
            $producto->cantidad
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
