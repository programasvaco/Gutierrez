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
        Schema::create('kardex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->foreignId('almacen_id')->constrained('almacenes')->onDelete('restrict');
            $table->enum('documento', [
                'Compra', 
                'Cancelación de compra', 
                'Salida traspaso', 
                'Recepción traspaso'
            ]);
            $table->string('referencia_doc', 50);
            $table->date('fecha');
            $table->time('hora');
            $table->enum('tipo_movimiento', ['Entrada', 'Salida']);
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo', 10, 2);
            $table->decimal('existencia_anterior', 10, 2)->default(0);
            $table->decimal('existencia_nueva', 10, 2)->default(0);
            $table->timestamps();
            
            // Índices para mejorar las búsquedas
            $table->index(['producto_id', 'almacen_id', 'fecha']);
            $table->index('referencia_doc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
