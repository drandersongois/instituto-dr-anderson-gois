<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agendamento extends Model
{
    public const STATUS = [
        'agendado' => 'Agendado',
        'confirmado' => 'Confirmado',
        'concluido' => 'Concluído',
        'cancelado' => 'Cancelado',
    ];

    public const FORMAS_PAGAMENTO = [
        'dinheiro' => 'Dinheiro',
        'pix' => 'Pix',
        'cartao' => 'Cartão',
    ];

    protected $fillable = [
        'cliente_id',
        'evento_id',
        'data_hora',
        'status',
        'observacoes',
        'pago',
        'data_pagamento',
        'forma_pagamento',
    ];

    protected function casts(): array
    {
        return [
            'data_hora' => 'datetime',
            'pago' => 'boolean',
            'data_pagamento' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
