<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Jobs\RecordatorioCitasJob;
use App\Jobs\RevisarInventarioJob;

class JobWebController extends Controller
{
    public function probarInventario()
    {
        RevisarInventarioJob::dispatch();

        return 'Job enviado correctamente';
    }

    public function probarRecordatorios()
    {
        RecordatorioCitasJob::dispatch();

        return 'Job de recordatorios enviado';
    }
}
