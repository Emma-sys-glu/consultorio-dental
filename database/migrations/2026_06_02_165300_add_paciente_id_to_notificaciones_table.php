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
    Schema::table('notificaciones', function (Blueprint $table) {
        $table->foreignId('paciente_id')
            ->nullable()
            ->after('id')
            ->constrained('pacientes')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('notificaciones', function (Blueprint $table) {
        $table->dropForeign(['paciente_id']);
        $table->dropColumn('paciente_id');
    });
}
};
