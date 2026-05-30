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
    Schema::create('tratamientos', function (Blueprint $table) {
        $table->id();

        $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('cascade');
        $table->foreignId('dentista_id')->constrained('dentistas')->onDelete('cascade');
        $table->foreignId('expediente_id')->constrained('expedientes')->onDelete('cascade');
        $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');

        $table->string('nombre');
        $table->text('descripcion')->nullable();
        $table->decimal('costo', 10, 2)->default(0);
        $table->enum('estado', ['pendiente', 'en_proceso', 'finalizado', 'cancelado'])->default('pendiente');
        $table->date('fecha_inicio');
        $table->date('fecha_fin')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
