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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('codigo_empaque', 50)->nullable();
            $table->string('descripcion', 255);
            $table->string('unidad', 50);
            $table->string('unidad_compra', 50);
            $table->decimal('contenido', 10, 2)->default(1);
            $table->integer('stock_min')->default(0);
            $table->integer('stock_max')->default(0);
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
