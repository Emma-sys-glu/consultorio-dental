<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->foreignId('inventario_id')
                ->nullable()
                ->constrained('inventarios')
                ->onDelete('set null')
                ->after('tratamiento_id');

            $table->integer('cantidad')->nullable()->after('inventario_id');
        });
    }

    public function down(): void
    {
        Schema::table('recetas', function (Blueprint $table) {
            $table->dropForeign(['inventario_id']);
            $table->dropColumn(['inventario_id', 'cantidad']);
        });
    }
};
