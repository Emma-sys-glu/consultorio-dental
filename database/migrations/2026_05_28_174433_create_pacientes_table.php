<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('pacientes', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('apellido_paterno');
        $table->string('apellido_materno')->nullable();
        $table->string('telefono');
        $table->string('correo')->unique();
        $table->date('fecha_nacimiento');
        $table->string('curp')->nullable();
        $table->string('tipo_sangre')->nullable();
        $table->string('alergias')->nullable();
        $table->text('antecedentes_medicos')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
