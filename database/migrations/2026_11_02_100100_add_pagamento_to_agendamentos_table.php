<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->boolean('pago')->default(false);
            $table->date('data_pagamento')->nullable();
            $table->string('forma_pagamento', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['pago', 'data_pagamento', 'forma_pagamento']);
        });
    }
};
