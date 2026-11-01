<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->foreignId('evento_id')->constrained('eventos')->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('data_hora');
            $table->string('status', 20)->default('agendado');
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->unique(['cliente_id', 'evento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
