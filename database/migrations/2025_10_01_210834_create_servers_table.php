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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nombre del servidor
            $table->string('url'); // URL del servidor
            $table->enum('type', ['web', 'api', 'ftp', 'bd']); // Tipo de servidor
            $table->text('description')->nullable(); // Descripción opcional
            $table->boolean('is_active')->default(true); // Estado activo/inactivo
            $table->timestamp('last_checked')->nullable(); // Última verificación
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
