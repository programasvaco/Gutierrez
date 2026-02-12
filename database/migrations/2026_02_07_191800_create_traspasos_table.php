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
        Schema::create('traspasos', function (Blueprint $table) {
            $table->id();
            $table->string('folio', 50)->unique();
            $table->date('fecha');
            $table->time('hora');
            $table->foreignId('almacen_origen_id')->constrained('almacenes')->onDelete('restrict');
            $table->foreignId('almacen_destino_id')->constrained('almacenes')->onDelete('restrict');
            $table->enum('status', ['creado', 'en transito', 'recibido', 'cancelado'])->default('creado');
            $table->timestamp('fecha_transito')->nullable();
            $table->timestamp('fecha_recepcion')->nullable();
            $table->timestamp('fecha_cancelacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index('status');
            $table->index(['almacen_origen_id', 'fecha']);
            $table->index(['almacen_destino_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traspasos');
    }
};
