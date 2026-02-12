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
        Schema::create('inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('restrict');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->decimal('existencia', 10, 2)->default(0);
            $table->timestamps();
            
            // Índice único para evitar duplicados de producto-almacén
            $table->unique(['almacen_id', 'producto_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario');
    }
};
