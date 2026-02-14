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
        Schema::table('productos', function (Blueprint $table) {
            // agrega los campos de precio
            $table->decimal('precio_venta', 10, 2)->default(0)->after('stock_max');
            $table->decimal('precio_minimo', 10, 2)->default(0)->after('precio_venta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            //
            $table->dropColumn(['precio_venta', 'precio_minimo']);
        });
    }
};
