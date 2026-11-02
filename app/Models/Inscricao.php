<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    protected $table = 'inscricoes';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'cpf',
        'data_nascimento',
        'sexo',
        'endereco',
        'cro',
        'mensagem',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function dataInscricao(): CarbonInterface
    {
        return $this->created_at->copy()->setTimezone('America/Bahia');
    }

    public function cpfFormatado(): string
    {
        return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $this->cpf);
    }
}
