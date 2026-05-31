<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expediente_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expediente_id')
                ->constrained('expedientes')
                ->onDelete('cascade');
            $table->string('tipo')->default('Documento clinico');
            $table->string('nombre_original');
            $table->string('ruta');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('tamano')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expediente_documentos');
    }
};
