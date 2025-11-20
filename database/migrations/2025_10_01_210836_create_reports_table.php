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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers')->onDelete('cascade');
            $table->string('servidor_nombre'); // Nombre del servidor en el reporte
            $table->date('fecha'); // Fecha del reporte
            $table->time('hora'); // Hora del reporte
            $table->string('duracion')->nullable(); // Duración del incidente
            $table->text('error_descripcion'); // Descripción del error
            $table->string('resuelto_por')->nullable(); // Quién lo resolvió
            $table->enum('status', ['pendiente', 'en_proceso', 'resuelto'])->default('pendiente');
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
