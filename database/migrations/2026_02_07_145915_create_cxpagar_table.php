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
        Schema::create('cxpagar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->onDelete('restrict');
            $table->foreignId('compra_id')->constrained('compras')->onDelete('cascade');
            $table->date('fecha');
            $table->date('fecha_vencimiento');
            $table->decimal('importe', 12, 2);
            $table->decimal('saldo', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cxpagar');
    }
};
