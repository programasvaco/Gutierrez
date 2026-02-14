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
        Schema::table('kardex', function (Blueprint $table) {
            //agrea documentos
            $table->enum('documento', [
                'Compra', 
                'Cancelación de compra', 
                'Salida traspaso', 
                'Recepción traspaso',
                'Inventario inicial',
                'Ajuste de inventario'
            ])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kardex', function (Blueprint $table) {
            //
            $table->enum('documento', [
                'Compra', 
                'Cancelación de compra', 
                'Salida traspaso', 
                'Recepción traspaso'
            ])->change();
        });
    }
};
