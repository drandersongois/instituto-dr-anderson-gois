<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Um "evento" do Instituto é um curso.
 */
class Evento extends Model
{
    public const AREAS = ['Ortodontia', 'DTM e Bruxismo', 'Estética', 'Harmonização'];

    protected $fillable = [
        'titulo',
        'area',
        'cidade',
        'data_inicio',
        'carga_horaria',
        'valor',
        'descricao',
        'privado',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'valor' => 'decimal:2',
            'privado' => 'boolean',
        ];
    }

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }

    public function imagemUrl(): string
    {
        return asset('img/eventos_img/'.($this->imagem ?: 'curso-padrao.svg'));
    }
}
