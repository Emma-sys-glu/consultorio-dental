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
                'Prueba de notificación'
            );
            $this->info("  ✓ Push enviado a {$email}");
        } catch (\Throwable $e) {
            $this->error("  ✗ Error: " . $e->getMessage());
        }
    }
}
