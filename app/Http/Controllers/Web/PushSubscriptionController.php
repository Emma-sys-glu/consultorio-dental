<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function guardar(Request $request)
    {
        $data = $request->validate([
            'endpoint'                  => 'required|string',
            'keys.p256dh'               => 'required|string',
            'keys.auth'                 => 'required|string',
        ]);

        PushSubscription::updateOrCreate(
            ['endpoint' => $data['endpoint']],
            [
                'user_id'    => auth()->id(),
                'public_key' => $data['keys']['p256dh'],
                'auth_token' => $data['keys']['auth'],
            ]
        );

        return response()->json(['ok' => true]);
    }

    public function eliminar(Request $request)
    {
        PushSubscription::where('user_id', auth()->id())->delete();

        return response()->json(['ok' => true]);
    }
}
