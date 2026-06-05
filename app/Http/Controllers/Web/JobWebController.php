<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\RecordatorioCitasJob;
use App\Jobs\RevisarInventarioJob;

class JobWebController extends Controller
{
    public function probarInventario()
    {
        RevisarInventarioJob::dispatchSync();
        return 'Job de inventario ejecutado — push enviado a admin/recepcionistas con suscripción.';
    }

    public function probarRecordatorios()
    {
        RecordatorioCitasJob::dispatchSync();
        return 'Job de recordatorio (1 día antes) ejecutado.';
    }
}
