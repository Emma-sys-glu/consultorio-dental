<?php

namespace Database\Seeders;

use App\Models\Notificacion;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class NotificacionSeeder extends Seeder
{
    public function run(): void
    {
        $horas = ['09:00', '10:30', '11:00', '14:00', '15:30', '16:00'];

        $plantillas = [
            'recordatorio_cita' => [
                [
                    'titulo'  => 'Recordatorio de cita',
                    'mensaje' => 'Le recordamos que tiene una cita programada para mañana a las {hora}. Por favor confirme su asistencia.',
                ],
                [
                    'titulo'  => 'Confirme su cita',
                    'mensaje' => 'Su cita dental está programada para mañana a las {hora}. Le esperamos puntualmente.',
                ],
            ],
            'resultado_tratamiento' => [
                [
                    'titulo'  => 'Tratamiento finalizado',
                    'mensaje' => 'Su tratamiento dental ha concluido exitosamente. Recuerde seguir las indicaciones de su dentista.',
                ],
                [
                    'titulo'  => 'Revisión de seguimiento',
                    'mensaje' => 'Ha transcurrido una semana desde su tratamiento. Le recomendamos agendar una revisión de seguimiento.',
                ],
            ],
            'pago_pendiente' => [
                [
                    'titulo'  => 'Saldo pendiente',
                    'mensaje' => 'Tiene un saldo pendiente de pago por su tratamiento dental. Por favor contáctenos para regularizar su situación.',
                ],
            ],
            'promocion' => [
                [
                    'titulo'  => 'Promoción especial',
                    'mensaje' => 'Este mes contamos con un 20% de descuento en limpiezas dentales. ¡Agenda tu cita ahora!',
                ],
                [
                    'titulo'  => 'Blanqueamiento dental',
                    'mensaje' => 'Aprovecha nuestro paquete especial de blanqueamiento dental con precio preferencial este mes.',
                ],
            ],
            'aviso' => [
                [
                    'titulo'  => 'Aviso importante',
                    'mensaje' => 'Le informamos que nuestro consultorio estará cerrado el próximo día festivo. Puede reagendar su cita en línea.',
                ],
                [
                    'titulo'  => 'Cambio de horario',
                    'mensaje' => 'Informamos que el horario de atención se ha modificado. Contáctenos para confirmar su próxima cita.',
                ],
            ],
        ];

        $tipos       = array_keys($plantillas);
        $pacienteIds = Paciente::pluck('id')->toArray();

        $rows = [];
        for ($i = 0; $i < 500; $i++) {
            $tipo      = $tipos[array_rand($tipos)];
            $plantilla = $plantillas[$tipo][array_rand($plantillas[$tipo])];

            $rows[] = [
                'paciente_id' => $pacienteIds[array_rand($pacienteIds)],
                'tipo'        => $tipo,
                'titulo'      => $plantilla['titulo'],
                'mensaje'     => str_replace('{hora}', $horas[array_rand($horas)], $plantilla['mensaje']),
                'leida'       => fake()->boolean(40),
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        Notificacion::insert($rows);
    }
}
