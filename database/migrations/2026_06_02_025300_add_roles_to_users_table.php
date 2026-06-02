<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('users', function (Blueprint $table) {

        if (!Schema::hasColumn('users', 'paciente_id')) {
            $table->foreignId('paciente_id')
                ->nullable()
                ->constrained('pacientes')
                ->nullOnDelete();
        }

        if (!Schema::hasColumn('users', 'dentista_id')) {
            $table->foreignId('dentista_id')
                ->nullable()
                ->constrained('dentistas')
                ->nullOnDelete();
        }
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {

        if (Schema::hasColumn('users', 'paciente_id')) {
            $table->dropForeign(['paciente_id']);
            $table->dropColumn('paciente_id');
        }

        if (Schema::hasColumn('users', 'dentista_id')) {
            $table->dropForeign(['dentista_id']);
            $table->dropColumn('dentista_id');
        }
    });
}
};
