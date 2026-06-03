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
            DB::connection('pgsql_principal')->select('SELECT 1');
            $ms = (microtime(true) - $inicio) * 1000;

            if ($ms > env('DB_FAILOVER_TIMEOUT_MS', 1500)) {
                Log::warning("Principal lento ({$ms}ms). Activando respaldo.");
                $this->activar('pgsql_respaldo');
            } else {
                $this->activar('pgsql_principal');
            }
        } catch (\Throwable $e) {
            Log::error('Principal caido. Activando respaldo: ' . $e->getMessage());
            $this->activar('pgsql_respaldo');
        }

        return $next($request);
    }

    private function activar(string $conexion): void
    {
        // Purgar solo al respaldo para forzar reconexion fresca;
        // el principal ya tiene conexion abierta del SELECT 1
        if ($conexion === 'pgsql_respaldo') {
            DB::purge('pgsql_respaldo');
        }

        Config::set('database.default', $conexion);
    }
}
