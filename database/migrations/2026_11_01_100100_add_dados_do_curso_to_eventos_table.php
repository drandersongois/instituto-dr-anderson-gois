<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            // O upload é opcional: sem imagem, o site usa a capa padrão.
            $table->string('imagem')->nullable()->change();

            $table->string('area', 50)->nullable();
            $table->date('data_inicio')->nullable();
            $table->unsignedSmallInteger('carga_horaria')->nullable();
            $table->decimal('valor', 8, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('eventos', function (Blueprint $table) {
            $table->dropColumn(['area', 'data_inicio', 'carga_horaria', 'valor']);
        });
    }
};
