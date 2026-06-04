<?php

namespace App\Console\Commands;

use App\Models\Inventario;
use App\Models\PushSubscription;
use App\Models\User;
use App\Services\PushNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificacionInventarioTest extends Command
{
    protected $signature   = 'notif:inventario';
    protected $description = 'Prueba notificaciones push de alerta de inventario';

    public function handle(): void
    {
        $this->info('Revisando inventario...');

        $receptores = User::whereIn('rol', ['administrador', 'recepcionista'])
            ->whereHas('pushSubscriptions')
            ->get();

        $this->line('Receptores con push activo: ' . $receptores->count());
        foreach ($receptores as $u) {
            $this->line("  [{$u->rol}] {$u->email}");
        }

        if ($receptores->isEmpty()) {
            $this->warn('Sin receptores activos. Ningún admin o recepcionista tiene push activado.');
            return;
        }

        $stockBajo = Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->get();
        $porCaducar = Inventario::whereNotNull('fecha_caducidad')
            ->whereDate('fecha_caducidad', '<=', Carbon::now()->addDays(30))
            ->get();

        $this->line('Productos con stock bajo: ' . $stockBajo->count());
        foreach ($stockBajo->take(5) as $p) {
            $this->line("  {$p->nombre} — cantidad: {$p->cantidad}, minimo: {$p->stock_minimo}");
        }

        $this->line('Productos proximos a caducar: ' . $porCaducar->count());
        foreach ($porCaducar->take(5) as $p) {
            $this->line("  {$p->nombre} — vence: {$p->fecha_caducidad}");
        }

        if ($stockBajo->isEmpty() && $porCaducar->isEmpty()) {
            $titulo  = 'Inventario revisado';
            $mensaje = 'El inventario esta en orden, todos los productos con stock correcto.';
        } else {
            $total   = $stockBajo->count();
            $titulo  = "Alerta: {$total} producto(s) con stock bajo";
            $mensaje = $stockBajo->take(3)->map(fn($p) => $p->nombre)->implode(', ');
            if ($stockBajo->count() > 3) $mensaje .= ' y mas.';
        }

        $push = app(PushNotificationService::class);

        foreach ($receptores as $u) {
            try {
                $push->enviarAlUsuario($u->id, $titulo, $mensaje, '/vista-inventario-alertas');
                $this->info("Push enviado a: {$u->email}");
            } catch (\Throwable $e) {
                $this->error("Error con {$u->email}: " . $e->getMessage());
            }
        }

        $this->info('Listo.');
    }
}
