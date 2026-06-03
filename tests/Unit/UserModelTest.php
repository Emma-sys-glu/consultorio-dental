<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas unitarias del modelo User.
 *
 * Verifica que los métodos de verificación de rol funcionan
 * correctamente para los 4 roles del sistema.
 */
class UserModelTest extends TestCase
{
    #[Test]
    public function es_administrador_retorna_true_para_rol_administrador(): void
    {
        $user = User::factory()->make(['rol' => 'administrador']);
        $this->assertTrue($user->esAdministrador());
    }

    #[Test]
    public function es_administrador_retorna_false_para_otros_roles(): void
    {
        foreach (['recepcionista', 'dentista', 'paciente'] as $rol) {
            $user = User::factory()->make(['rol' => $rol]);
            $this->assertFalse($user->esAdministrador(), "Falló para rol: {$rol}");
        }
    }

    #[Test]
    public function es_recepcionista_retorna_true_para_rol_recepcionista(): void
    {
        $user = User::factory()->make(['rol' => 'recepcionista']);
        $this->assertTrue($user->esRecepcionista());
    }

    #[Test]
    public function es_dentista_retorna_true_para_rol_dentista(): void
    {
        $user = User::factory()->make(['rol' => 'dentista']);
        $this->assertTrue($user->esDentista());
    }

    #[Test]
    public function es_paciente_retorna_true_para_rol_paciente(): void
    {
        $user = User::factory()->make(['rol' => 'paciente']);
        $this->assertTrue($user->esPaciente());
    }

    #[Test]
    public function cada_metodo_es_exclusivo_de_su_propio_rol(): void
    {
        $admin = User::factory()->make(['rol' => 'administrador']);

        $this->assertTrue($admin->esAdministrador());
        $this->assertFalse($admin->esRecepcionista());
        $this->assertFalse($admin->esDentista());
        $this->assertFalse($admin->esPaciente());
    }

    #[Test]
    public function user_factory_genera_usuario_con_campos_requeridos(): void
    {
        $user = User::factory()->make();

        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotEmpty($user->password);
        $this->assertNotEmpty($user->rol);
    }

    #[Test]
    public function el_modelo_no_expone_password_ni_remember_token_en_array(): void
    {
        $user = User::factory()->make();
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }
}
