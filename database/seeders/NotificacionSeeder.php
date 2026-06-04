<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        $horas = ['09:00', '10:30', '11:00', '14:00', '15:30', '16:00', '17:00'];

        $plantillas = [
            'recordatorio_cita' => [
                ['titulo' => 'Recordatorio de cita',  'mensaje' => 'Le recordamos que tiene una cita programada para mañana a las {hora}. Por favor confirme su asistencia.'],
                ['titulo' => 'Confirme su cita',      'mensaje' => 'Su cita dental está programada para mañana a las {hora}. Le esperamos puntualmente.'],
                ['titulo' => 'Cita próxima',           'mensaje' => 'No olvide su cita dental de hoy a las {hora}. Llegue 10 minutos antes.'],
            ],
            'resultado_tratamiento' => [
                ['titulo' => 'Tratamiento finalizado', 'mensaje' => 'Su tratamiento dental ha concluido exitosamente. Recuerde seguir las indicaciones de su dentista.'],
                ['titulo' => 'Revisión de seguimiento','mensaje' => 'Ha transcurrido una semana desde su tratamiento. Le recomendamos agendar una revisión de seguimiento.'],
                ['titulo' => 'Alta médica',             'mensaje' => 'Su tratamiento ha sido completado satisfactoriamente. Recuerde sus citas de mantenimiento.'],
            ],
            'pago_pendiente' => [
                ['titulo' => 'Saldo pendiente',        'mensaje' => 'Tiene un saldo pendiente de pago por su tratamiento dental. Por favor contáctenos para regularizar.'],
                ['titulo' => 'Recordatorio de pago',   'mensaje' => 'Su próxima cuota de tratamiento vence en 3 días. Contáctenos para realizar su pago.'],
            ],
            'promocion' => [
                ['titulo' => 'Promoción especial',     'mensaje' => 'Este mes contamos con un 20% de descuento en limpiezas dentales. ¡Agenda tu cita ahora!'],
                ['titulo' => 'Blanqueamiento dental',  'mensaje' => 'Aprovecha nuestro paquete especial de blanqueamiento dental con precio preferencial.'],
                ['titulo' => 'Ortodoncia invisible',   'mensaje' => 'Consulta nuestros planes de ortodoncia sin brackets. Financiamiento disponible.'],
            ],
            'aviso' => [
                ['titulo' => 'Aviso importante',       'mensaje' => 'Nuestro consultorio estará cerrado el próximo día festivo. Puede reagendar su cita en línea.'],
                ['titulo' => 'Cambio de horario',      'mensaje' => 'Informamos que el horario de atención se ha modificado. Contáctenos para confirmar su próxima cita.'],
                ['titulo' => 'Nuevo servicio',         'mensaje' => 'Contamos con nuevo servicio de implantes dentales. Solicite su consulta de valoración sin costo.'],
            ],
        ];

        $tipos       = array_keys($plantillas);
        $pacienteIds = DB::table('pacientes')->pluck('id')->toArray();

        $rows  = [];
        $batch = 500;

        for ($i = 0; $i < 3000; $i++) {
            $tipo      = $tipos[array_rand($tipos)];
            $plantilla = $plantillas[$tipo][array_rand($plantillas[$tipo])];

            $rows[] = [
                'paciente_id' => $pacienteIds[array_rand($pacienteIds)],
                'tipo'        => $tipo,
                'titulo'      => $plantilla['titulo'],
                'mensaje'     => str_replace('{hora}', $horas[array_rand($horas)], $plantilla['mensaje']),
                'leida'       => mt_rand(0, 1),
                'created_at'  => date('Y-m-d H:i:s', strtotime('-' . mt_rand(0, 180) . ' days')),
                'updated_at'  => now(),
            ];

            if (count($rows) >= $batch) {
                DB::table('notificaciones')->insert($rows);
                $rows = [];
            }
        }

        if (!empty($rows)) {
            DB::table('notificaciones')->insert($rows);
        }
    }
}
