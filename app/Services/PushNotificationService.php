<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('pwa.vapid_subject'),
                'publicKey'  => config('pwa.vapid_public_key'),
                'privateKey' => config('pwa.vapid_private_key'),
            ],
        ]);
    }

    public function enviarAlUsuario(int $userId, string $titulo, string $mensaje, string $url = '/'): void
    {
        $suscripciones = PushSubscription::where('user_id', $userId)->get();

        if ($suscripciones->isEmpty()) {
            return;
        }

        $payload = json_encode([
            'title' => $titulo,
            'body'  => $mensaje,
            'url'   => $url,
        ]);

        foreach ($suscripciones as $sub) {
            $this->webPush->queueNotification(
                Subscription::create([
                    'endpoint'        => $sub->endpoint,
                    'publicKey'       => $sub->public_key,
                    'authToken'       => $sub->auth_token,
                    'contentEncoding' => 'aesgcm',
                ]),
                $payload
            );
        }

        // Enviar y limpiar suscripciones expiradas
        foreach ($this->webPush->flush() as $report) {
            if ($report->isSubscriptionExpired()) {
                PushSubscription::where('endpoint', $report->getEndpoint())->delete();
            }
        }
    }
}
