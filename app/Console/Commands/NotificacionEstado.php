<?php

namespace App\Console\Commands;

use App\Models\Cita;
use App\Models\Inventario;
use App\Models\Notificacion;
use App\Models\PushSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotificacionEstado extends Command
{
    protected $signature   = 'notif:estado';
    protected $description = 'Muestra el estado del sistema de notificaciones push';

    public function handle(): void
    {
        $this->info('--- Suscripciones push ---');
        $subs = PushSubscription::with('user')->get();
        $this->line('Total: ' . $subs->count());
        foreach ($subs->groupBy(fn($s) => optional($s->user)->rol ?? '?') as $rol => $grupo) {
            $this->line("[{$rol}] {$grupo->count()} suscripcion(es)");
            foreach ($grupo->take(3) as $s) {
                $this->line('  ' . (optional($s->user)->email ?? "user #{$s->user_id}"));
            }
            if ($grupo->count() > 3) {
                $this->line('  ... y ' . ($grupo->count() - 3) . ' mas');
            }
        }
        if ($subs->isEmpty()) {
            $this->warn('Sin suscripciones. Los usuarios deben aceptar permisos en el navegador.');
        }

        $this->newLine();
        $this->info('--- Inventario ---');
        $bajo    = Inventario::whereColumn('cantidad', '<=', 'stock_minimo')->count();
        $caducar = Inventario::whereNotNull('fecha_caducidad')
            ->whereDate('fecha_caducidad', '<=', Carbon::now()->addDays(30))->count();
        $this->line("Productos con stock bajo: {$bajo}");
        $this->line("Productos por caducar (30 dias): {$caducar}");

        $this->newLine();
        $this->info('--- Citas proximas ---');
        $manana = Cita::whereDate('fecha', Carbon::tomorrow())->whereNotIn('estado', ['cancelada'])->count();
        $en2h   = Cita::whereDate('fecha', Carbon::today())
            ->whereTime('hora_inicio', '>=', Carbon::now()->addMinutes(110)->format('H:i:s'))
            ->whereTime('hora_inicio', '<=', Carbon::now()->addMinutes(130)->format('H:i:s'))
            ->whereNotIn('estado', ['cancelada'])->count();
        $this->line("Citas manana (recordatorio 1d): {$manana}");
        $this->line("Citas en ~2h (recordatorio 2h): {$en2h}");

        $this->newLine();
        $this->info('--- Ultimas notificaciones hoy ---');
        $recientes = Notificacion::whereDate('created_at', Carbon::today())
            ->orderByDesc('id')->limit(5)->get();
        if ($recientes->isEmpty()) {
            $this->line('Sin notificaciones hoy.');
        }
        foreach ($recientes as $n) {
            $this->line("[#{$n->id}] {$n->tipo} — {$n->titulo} — paciente {$n->paciente_id} — {$n->created_at->format('H:i')}");
        }

        $this->newLine();
        $this->info('--- Comandos disponibles ---');
        $this->line('php artisan notif:estado');
        $this->line('php artisan notif:inventario');
        $this->line('php artisan notif:cita {email}');
        $this->line('php artisan notif:cita {email} --tipo=2h');
        $this->line('php artisan push:test');
        $this->line('php artisan push:test {email}');
    }
}
