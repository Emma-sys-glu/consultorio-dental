<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->integer('stock_c1')->default(0)->after('cantidad');
            $table->integer('stock_c2')->default(0)->after('stock_c1');
            $table->integer('stock_c3')->default(0)->after('stock_c2');
            $table->integer('stock_c4')->default(0)->after('stock_c3');
        });
    }

    public function down(): void
    {
        Schema::table('inventarios', function (Blueprint $table) {
            $table->dropColumn(['stock_c1', 'stock_c2', 'stock_c3', 'stock_c4']);
        });
    }
};
