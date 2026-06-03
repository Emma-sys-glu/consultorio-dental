<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use App\Models\User;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class TestPushNotification extends Command
{
    protected $signature   = 'push:test {email? : Email del usuario (opcional)}';
    protected $description = 'Envía un push de prueba. Sin argumento muestra el estado de suscripciones.';

    public function handle(): void
    {
        $total = PushSubscription::count();
        $this->info("Suscripciones push en BD: {$total}");

        if ($total === 0) {
            $this->warn('No hay suscripciones guardadas.');
            $this->line('');
            $this->line('Pasos para suscribirse:');
            $this->line('  1. Abre https://dentaltec.mexicocentral.cloudapp.azure.com en Chrome/Edge/Firefox');
            $this->line('  2. Inicia sesión con un usuario paciente');
            $this->line('  3. Acepta el permiso de notificaciones cuando el navegador lo pida');
            $this->line('  4. Abre DevTools → Consola y busca mensajes [PWA Push]');
            $this->line('  5. Vuelve a correr: php artisan push:test');
            return;
        }

        $email = $this->argument('email');

        if ($email) {
            $user = User::where('email', $email)->first();
            if (! $user) {
                $this->error("Usuario {$email} no encontrado.");
                return;
            }
            $subs = PushSubscription::where('user_id', $user->id)->get();
            if ($subs->isEmpty()) {
                $this->warn("El usuario {$email} no tiene suscripción push.");
                return;
            }
            $this->enviar($user->id, $user->email);
        } else {
            // Enviar a todos los suscritos
            PushSubscription::with('user')->get()
                ->groupBy('user_id')
                ->each(function ($subs, $userId) {
                    $email = optional($subs->first()->user)->email ?? "user #{$userId}";
                    $this->enviar($userId, $email);
                });
        }
    }

    private function enviar(int $userId, string $email): void
    {
        $this->line("Enviando push a {$email}...");
        try {
            app(PushNotificationService::class)->enviarAlUsuario(
                $userId,
                'Prueba de notificación',
                'Si ves esto, ¡las notificaciones push funcionan correctamente!',
                '/notificaciones'
            );
            $this->info("  ✓ Push enviado a {$email}");
        } catch (\Throwable $e) {
            $this->error("  ✗ Error: " . $e->getMessage());
        }
    }
}
