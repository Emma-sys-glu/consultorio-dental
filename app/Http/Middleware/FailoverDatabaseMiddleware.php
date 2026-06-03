<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FailoverDatabaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {

            $inicio = microtime(true);

            DB::connection('pgsql_principal')
                ->select('SELECT 1');

            $tiempo = (microtime(true) - $inicio) * 1000;

            if ($tiempo > env('DB_FAILOVER_TIMEOUT_MS', 1500)) {

                Log::warning(
                    'Base principal lenta (' .
                    round($tiempo, 2) .
                    ' ms). Activando respaldo.'
                );

                $this->usarRespaldo();

            } else {

                $this->usarPrincipal();
            }

        } catch (\Throwable $e) {

            Log::error(
                'Principal fuera de servicio. Activando respaldo. Error: '
                . $e->getMessage()
            );

            $this->usarRespaldo();
        }

        return $next($request);
    }

    private function usarPrincipal(): void
    {
        Config::set(
            'database.default',
            'pgsql_principal'
        );

        DB::purge('pgsql_principal');
    }

    private function usarRespaldo(): void
    {
        Config::set(
            'database.default',
            'pgsql_respaldo'
        );
        

        DB::purge('pgsql_respaldo');
    }
}