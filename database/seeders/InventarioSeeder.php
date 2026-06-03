<?php

namespace Database\Seeders;

use App\Models\Inventario;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = [
            'Dental Depot México',
            'Patterson Dental',
            'Henry Schein',
            'DentalEZ México',
            'Farmacéutica Nacional',
        ];

        $items = [
            ['nombre' => 'Resina compuesta A1',                        'categoria' => 'Material dental',  'precio_unitario' => 450.00],
            ['nombre' => 'Resina compuesta A2',                        'categoria' => 'Material dental',  'precio_unitario' => 450.00],
            ['nombre' => 'Resina compuesta B2',                        'categoria' => 'Material dental',  'precio_unitario' => 450.00],
            ['nombre' => 'Amalgama dental',                            'categoria' => 'Material dental',  'precio_unitario' => 320.00],
            ['nombre' => 'Cemento de ionómero de vidrio',              'categoria' => 'Material dental',  'precio_unitario' => 580.00],
            ['nombre' => 'Hidróxido de calcio',                        'categoria' => 'Material dental',  'precio_unitario' => 210.00],
            ['nombre' => 'Alginato tipo II (kg)',                      'categoria' => 'Material dental',  'precio_unitario' => 310.00],
            ['nombre' => 'Yeso piedra tipo III (kg)',                  'categoria' => 'Material dental',  'precio_unitario' => 85.00],
            ['nombre' => 'Cera de oclusión',                           'categoria' => 'Material dental',  'precio_unitario' => 95.00],
            ['nombre' => 'Eugenol líquido',                            'categoria' => 'Material dental',  'precio_unitario' => 160.00],
            ['nombre' => 'Espejo dental #5',                           'categoria' => 'Instrumental',     'precio_unitario' => 85.00],
            ['nombre' => 'Explorador doble',                           'categoria' => 'Instrumental',     'precio_unitario' => 95.00],
            ['nombre' => 'Pinza de algodón',                           'categoria' => 'Instrumental',     'precio_unitario' => 75.00],
            ['nombre' => 'Jeringa carpule',                            'categoria' => 'Instrumental',     'precio_unitario' => 320.00],
            ['nombre' => 'Curetas periodontales (juego)',              'categoria' => 'Instrumental',     'precio_unitario' => 890.00],
            ['nombre' => 'Lidocaína 2% con epinefrina (caja 50 amp)', 'categoria' => 'Anestésico',       'precio_unitario' => 1200.00],
            ['nombre' => 'Mepivacaína 3% (caja 50 amp)',              'categoria' => 'Anestésico',       'precio_unitario' => 1100.00],
            ['nombre' => 'Articaína 4% (caja 50 amp)',                'categoria' => 'Anestésico',       'precio_unitario' => 1350.00],
            ['nombre' => 'Amoxicilina 500mg (caja 100)',              'categoria' => 'Medicamento',      'precio_unitario' => 480.00],
            ['nombre' => 'Ibuprofeno 400mg (caja 100)',               'categoria' => 'Medicamento',      'precio_unitario' => 220.00],
            ['nombre' => 'Clindamicina 300mg (caja 50)',              'categoria' => 'Medicamento',      'precio_unitario' => 650.00],
            ['nombre' => 'Metronidazol 500mg (caja 50)',              'categoria' => 'Medicamento',      'precio_unitario' => 380.00],
            ['nombre' => 'Clorhexidina 0.12% (litro)',               'categoria' => 'Medicamento',      'precio_unitario' => 180.00],
            ['nombre' => 'Guantes de látex M (caja 100)',            'categoria' => 'Desechable',       'precio_unitario' => 280.00],
            ['nombre' => 'Guantes de látex L (caja 100)',            'categoria' => 'Desechable',       'precio_unitario' => 280.00],
            ['nombre' => 'Cubrebocas tricapa (caja 50)',             'categoria' => 'Desechable',       'precio_unitario' => 150.00],
            ['nombre' => 'Baberos desechables (paquete 500)',        'categoria' => 'Desechable',       'precio_unitario' => 320.00],
            ['nombre' => 'Rollos de algodón (bolsa 200)',            'categoria' => 'Desechable',       'precio_unitario' => 95.00],
            ['nombre' => 'Agujas dentales cortas 27G (caja 100)',    'categoria' => 'Desechable',       'precio_unitario' => 380.00],
            ['nombre' => 'Fresas de diamante redonda (paquete 5)',   'categoria' => 'Desechable',       'precio_unitario' => 290.00],
            ['nombre' => 'Papel articular (caja 12 libros)',         'categoria' => 'Accesorio',        'precio_unitario' => 120.00],
            ['nombre' => 'Gasa estéril 10x10 (caja 100)',           'categoria' => 'Desechable',       'precio_unitario' => 145.00],
            ['nombre' => 'Dique de goma (caja 36)',                  'categoria' => 'Desechable',       'precio_unitario' => 260.00],
        ];

        foreach ($items as $item) {
            Inventario::create(array_merge($item, [
                'cantidad'        => rand(5, 200),
                'stock_minimo'    => rand(5, 20),
                'fecha_caducidad' => fake()->optional(0.6)->dateTimeBetween('+3 months', '+3 years')?->format('Y-m-d'),
                'proveedor'       => $proveedores[array_rand($proveedores)],
            ]));
        }
    }
}
