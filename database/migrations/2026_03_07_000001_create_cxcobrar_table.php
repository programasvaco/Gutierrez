<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cxcobrar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('restrict');
            $table->date('fecha');
            $table->date('fecha_vencimiento');
            $table->decimal('importe', 12, 2);
            $table->decimal('saldo', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cxcobrar');
    }
};
