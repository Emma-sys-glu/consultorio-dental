<?php

namespace App\Jobs;

use App\Models\Inventario;
use App\Models\User;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class RevisarInventarioJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $push = app(PushNotificationService::class);

        // ── Receptores: admin y recepcionistas con suscripción push ──
        $receptores = User::whereIn('rol', ['administrador', 'recepcionista'])
            ->whereHas('pushSubscriptions')
            ->pluck('id');

        if ($receptores->isEmpty()) {
            Log::info('[Inventario] Sin receptores push (admin/recepcionista).');
            return;
        }

        // ── 1. Productos con stock general bajo ──────────────────────
        $stockBajo = Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->get();

        if ($stockBajo->isNotEmpty()) {
            $lista = $stockBajo->take(5)->map(fn($p) => "{$p->nombre} ({$p->cantidad}/{$p->stock_minimo})")->implode(', ');
            $sufijo = $stockBajo->count() > 5 ? ' y ' . ($stockBajo->count() - 5) . ' más.' : '.';

            $titulo  = '⚠ Stock bajo en inventario';
            $mensaje = $stockBajo->count() . ' producto(s) por debajo del mínimo: ' . $lista . $sufijo;

            foreach ($receptores as $userId) {
                $push->enviarAlUsuario($userId, $titulo, $mensaje, '/vista-inventario-alertas');
            }

            Log::warning('[Inventario] Stock bajo – notificados ' . $receptores->count() . ' usuarios. Productos: ' . $stockBajo->count());
        }

        // ── 2. Productos próximos a caducar (≤ 30 días) ──────────────
        $porCaducar = Inventario::whereNotNull('fecha_caducidad')
            ->whereDate('fecha_caducidad', '<=', Carbon::now()->addDays(30))
            ->get();

        if ($porCaducar->isNotEmpty()) {
            $lista = $porCaducar->take(5)->map(fn($p) => "{$p->nombre} (vence {$p->fecha_caducidad})")->implode(', ');
            $sufijo = $porCaducar->count() > 5 ? ' y ' . ($porCaducar->count() - 5) . ' más.' : '.';

            $titulo  = '⏰ Productos próximos a caducar';
            $mensaje = $porCaducar->count() . ' producto(s) caduca(n) en 30 días: ' . $lista . $sufijo;

            foreach ($receptores as $userId) {
                $push->enviarAlUsuario($userId, $titulo, $mensaje, '/vista-inventario-alertas');
            }

            Log::warning('[Inventario] Por caducar – notificados ' . $receptores->count() . ' usuarios. Productos: ' . $porCaducar->count());
        }

        // ── 3. Stock bajo por consultorio ────────────────────────────
        foreach ([1, 2, 3, 4] as $n) {
            $col = "stock_c{$n}";
            $bajo = Inventario::whereColumn($col, '<=', 'stock_minimo')->where($col, '>', 0)->get();

            if ($bajo->isNotEmpty()) {
                $lista = $bajo->take(3)->map(fn($p) => "{$p->nombre} ({$p->$col})")->implode(', ');
                $sufijo = $bajo->count() > 3 ? ' y más.' : '.';
                $titulo  = "⚠ Stock bajo en Consultorio {$n}";
                $mensaje = $bajo->count() . " producto(s) bajos en C{$n}: " . $lista . $sufijo;

                foreach ($receptores as $userId) {
                    $push->enviarAlUsuario($userId, $titulo, $mensaje, '/vista-inventario-alertas');
                }
            }
        }
    }
}
