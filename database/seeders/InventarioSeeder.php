<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = ['Dental Depot México', 'Patterson Dental', 'Henry Schein', 'DentalEZ México', 'Farmacéutica Nacional', 'Distribuidora Dental SA', 'MedSupply MX'];

        $categorias = [
            'Medicamentos' => [
                ['base' => 'Amoxicilina',    'caducidad' => true,  'presentaciones' => ['250mg', '500mg', '875mg'],         'precio' => [120, 220, 380]],
                ['base' => 'Ibuprofeno',     'caducidad' => true,  'presentaciones' => ['200mg', '400mg', '600mg'],         'precio' => [80,  180, 260]],
                ['base' => 'Paracetamol',    'caducidad' => true,  'presentaciones' => ['500mg', '1g'],                     'precio' => [60,  120]],
                ['base' => 'Clindamicina',   'caducidad' => true,  'presentaciones' => ['150mg', '300mg'],                  'precio' => [320, 580]],
                ['base' => 'Metronidazol',   'caducidad' => true,  'presentaciones' => ['250mg', '500mg'],                  'precio' => [150, 280]],
                ['base' => 'Diclofenaco',    'caducidad' => true,  'presentaciones' => ['25mg', '50mg', '75mg'],            'precio' => [90,  160, 240]],
                ['base' => 'Ketorolaco',     'caducidad' => true,  'presentaciones' => ['10mg', '30mg'],                    'precio' => [180, 350]],
                ['base' => 'Tramadol',       'caducidad' => true,  'presentaciones' => ['50mg', '100mg'],                   'precio' => [280, 480]],
                ['base' => 'Dexametasona',   'caducidad' => true,  'presentaciones' => ['4mg', '8mg'],                      'precio' => [200, 380]],
                ['base' => 'Clorhexidina',   'caducidad' => true,  'presentaciones' => ['0.12% 500ml', '0.20% 1L'],        'precio' => [120, 200]],
                ['base' => 'Lidocaína',      'caducidad' => true,  'presentaciones' => ['2% amp x50', '2% amp x100'],      'precio' => [750, 1400]],
                ['base' => 'Mepivacaína',    'caducidad' => true,  'presentaciones' => ['3% amp x50', '3% amp x100'],      'precio' => [700, 1300]],
                ['base' => 'Articaína',      'caducidad' => true,  'presentaciones' => ['4% amp x50', '4% amp x100'],      'precio' => [850, 1600]],
            ],
            'Material dental' => [
                ['base' => 'Resina compuesta', 'caducidad' => true, 'presentaciones' => ['A1 4g', 'A2 4g', 'A3 4g', 'B2 4g', 'C2 4g'],  'precio' => [420, 420, 420, 420, 420]],
                ['base' => 'Cemento ionómero', 'caducidad' => true, 'presentaciones' => ['tipo I 15g', 'tipo II 15g', 'tipo III 20g'],    'precio' => [380, 480, 560]],
                ['base' => 'Amalgama dental',  'caducidad' => true, 'presentaciones' => ['cápsulas x50', 'cápsulas x100'],               'precio' => [280, 520]],
                ['base' => 'Hidróxido calcio', 'caducidad' => true, 'presentaciones' => ['polvo 50g', 'pasta 15g'],                      'precio' => [180, 240]],
                ['base' => 'Alginato',         'caducidad' => true, 'presentaciones' => ['tipo I 500g', 'tipo II 500g', 'rápido 1kg'],    'precio' => [220, 280, 480]],
                ['base' => 'Yeso piedra',      'caducidad' => true, 'presentaciones' => ['tipo III 1kg', 'tipo IV 1kg'],                 'precio' => [85, 150]],
                ['base' => 'Eugenol',          'caducidad' => true, 'presentaciones' => ['líquido 30ml', 'polvo 15g'],                   'precio' => [120, 95]],
                ['base' => 'Oxido de zinc',    'caducidad' => true, 'presentaciones' => ['polvo 25g', 'pasta 45g'],                      'precio' => [80, 130]],
                ['base' => 'Adhesivo dental',  'caducidad' => true, 'presentaciones' => ['botella 5ml', 'kit 2 piezas'],                 'precio' => [350, 680]],
                ['base' => 'Sellador fosas',   'caducidad' => true, 'presentaciones' => ['frasco 5ml', 'frasco 10ml'],                   'precio' => [280, 480]],
            ],
            'Instrumental' => [
                ['base' => 'Espejo dental',      'caducidad' => false, 'presentaciones' => ['#4', '#5', '#6'],                          'precio' => [75, 85, 95]],
                ['base' => 'Explorador',         'caducidad' => false, 'presentaciones' => ['simple', 'doble', 'recto'],                'precio' => [80, 95, 85]],
                ['base' => 'Pinza de algodón',   'caducidad' => false, 'presentaciones' => ['recta 16cm', 'curva 16cm'],               'precio' => [70, 80]],
                ['base' => 'Jeringa carpule',    'caducidad' => false, 'presentaciones' => ['estándar', 'aspirante'],                  'precio' => [280, 350]],
                ['base' => 'Cureta periodontal', 'caducidad' => false, 'presentaciones' => ['Gracey #1-2', '#3-4', '#5-6', 'juego 6'], 'precio' => [180, 180, 180, 750]],
                ['base' => 'Periotomo',          'caducidad' => false, 'presentaciones' => ['recto', 'curvo'],                         'precio' => [320, 360]],
                ['base' => 'Porta amalgama',     'caducidad' => false, 'presentaciones' => ['plástico', 'metálico'],                   'precio' => [60, 140]],
                ['base' => 'Fórceps extracción', 'caducidad' => false, 'presentaciones' => ['sup anterior', 'inf molar', 'pedíatrico'],'precio' => [480, 520, 380]],
            ],
            'Protección' => [

                ['base' => 'Guantes látex',      'caducidad' => true,  'presentaciones' => ['S x100', 'M x100', 'L x100', 'XL x100'], 'precio' => [270, 280, 280, 290]],
                ['base' => 'Guantes nitrilo',    'caducidad' => true,  'presentaciones' => ['S x100', 'M x100', 'L x100'],            'precio' => [320, 330, 330]],
                ['base' => 'Cubrebocas tricapa', 'caducidad' => true,  'presentaciones' => ['x50', 'x100'],                           'precio' => [150, 280]],
                ['base' => 'Cubrebocas N95',     'caducidad' => true,  'presentaciones' => ['x10', 'x25'],                            'precio' => [280, 650]],
                ['base' => 'Careta facial',      'caducidad' => false, 'presentaciones' => ['plástico', 'con espuma'],                 'precio' => [45, 75]],
                ['base' => 'Lentes protección',  'caducidad' => false, 'presentaciones' => ['claro', 'amarillo'],                     'precio' => [85, 95]],
                ['base' => 'Dique de goma',      'caducidad' => true,  'presentaciones' => ['látex x36', 'nitrilo x36'],              'precio' => [240, 320]],
                ['base' => 'Baberos desechables','caducidad' => false, 'presentaciones' => ['x500 blancos', 'x500 azules'],           'precio' => [280, 300]],
            ],
            'Limpieza' => [
                ['base' => 'Rollos de algodón',  'caducidad' => true,  'presentaciones' => ['x200', 'x500'],                              'precio' => [65, 140]],
                ['base' => 'Gasa estéril',        'caducidad' => true,  'presentaciones' => ['5x5 x100', '10x10 x100'],                   'precio' => [95, 145]],
                ['base' => 'Agujas dentales',     'caducidad' => true,  'presentaciones' => ['27G corta x100', '30G larga x100'],         'precio' => [320, 360]],
                ['base' => 'Fresas diamante',     'caducidad' => false, 'presentaciones' => ['redonda x5', 'troncocónica x5', 'llama x5'],'precio' => [280, 290, 285]],
                ['base' => 'Papel articular',     'caducidad' => false, 'presentaciones' => ['rojo x12', 'azul x12', 'bicolor x12'],      'precio' => [110, 110, 125]],
                ['base' => 'Eyectores saliva',    'caducidad' => true,  'presentaciones' => ['blancos x100', 'verdes x100'],              'precio' => [60, 65]],
                ['base' => 'Puntas aspiración',   'caducidad' => true,  'presentaciones' => ['HVE x100', 'quirúrgicas x100'],            'precio' => [120, 180]],
            ],
        ];

        $rows  = [];
        $batch = 500;
        $seq   = 0;

        $total = 3000;
        $productosPorCategoria = intdiv($total, count($categorias));

        foreach ($categorias as $categoria => $productos) {
            $generados = 0;

            while ($generados < $productosPorCategoria) {
                foreach ($productos as $producto) {
                    foreach ($producto['presentaciones'] as $idx => $presentacion) {
                        if ($generados >= $productosPorCategoria) break 2;

                        $seq++;
                        $precioBase = $producto['precio'][$idx] ?? end($producto['precio']);
                        $total2     = mt_rand(20, 200);
                        $c1 = (int) round($total2 * (mt_rand(15, 30) / 100));
                        $c2 = (int) round($total2 * (mt_rand(15, 30) / 100));
                        $c3 = (int) round($total2 * (mt_rand(15, 30) / 100));
                        $c4 = max(0, $total2 - $c1 - $c2 - $c3);

                        $rows[] = [
                            'nombre'          => $producto['base'] . ' ' . $presentacion,
                            'categoria'       => $categoria,
                            'cantidad'        => $total2,
                            'stock_c1'        => $c1,
                            'stock_c2'        => $c2,
                            'stock_c3'        => $c3,
                            'stock_c4'        => $c4,
                            'stock_minimo'    => mt_rand(5, 20),
                            'fecha_caducidad' => $producto['caducidad'] ? $this->generarCaducidad($seq) : null,
                            'proveedor'       => $proveedores[array_rand($proveedores)],
                            'precio_unitario' => $precioBase * (mt_rand(90, 110) / 100),
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];

                        $generados++;

                        if (count($rows) >= $batch) {
                            DB::table('inventarios')->insert($rows);
                            $rows = [];
                        }
                    }
                }
            }
        }

        if (!empty($rows)) {
            DB::table('inventarios')->insert($rows);
        }
    }

    private function generarCaducidad(int $seq): string
    {
        $rango = ($seq % 20);

        if ($rango < 3) {
            return date('Y-m-d', strtotime('-' . mt_rand(1, 10) . ' days'));
        } elseif ($rango < 6) {
            return date('Y-m-d', strtotime('+' . mt_rand(1, 7) . ' days'));
        } elseif ($rango < 10) {
            return date('Y-m-d', strtotime('+' . mt_rand(8, 30) . ' days'));
        } else {
            return date('Y-m-d', strtotime('+' . mt_rand(1, 36) . ' months'));
        }
    }
}
