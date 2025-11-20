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
        Schema::create('server_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers')->onDelete('cascade');
            $table->enum('status', ['online', 'offline', 'maintenance', 'error'])->default('offline');
            $table->integer('response_time')->nullable(); // Tiempo de respuesta en ms
            $table->integer('http_status_code')->nullable(); // Código de estado HTTP
            $table->text('error_message')->nullable(); // Mensaje de error si aplica
            $table->timestamp('checked_at'); // Cuándo se verificó
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('server_status');
    }
};
