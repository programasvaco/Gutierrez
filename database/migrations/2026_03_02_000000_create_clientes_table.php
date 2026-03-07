<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->unique();
            $table->string('razon_social', 200)->nullable();
            $table->string('domicilio', 255)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('cpostal', 10)->nullable();
            $table->string('rfc', 13)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correoe', 100)->nullable();
            $table->enum('status', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
