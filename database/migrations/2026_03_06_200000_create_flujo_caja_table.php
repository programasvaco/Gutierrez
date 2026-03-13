<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flujo_caja', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->enum('tipo', ['Entrada', 'Salida']);
            $table->string('referencia', 50);
            $table->decimal('cantidad', 12, 2);
            $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('restrict');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flujo_caja');
    }
};
