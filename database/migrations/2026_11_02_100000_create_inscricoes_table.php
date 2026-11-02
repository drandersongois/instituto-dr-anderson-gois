<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evento_id')->constrained('eventos')->restrictOnDelete();
            $table->string('nome')->index();
            $table->string('email');
            $table->string('telefone', 30);
            $table->string('cpf', 11)->index();
            $table->date('data_nascimento');
            $table->string('sexo', 10);
            $table->string('endereco', 500);
            $table->string('cro', 30)->nullable();
            $table->text('mensagem')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscricoes');
    }
};
