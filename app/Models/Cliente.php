<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Um "cliente" do Instituto é um aluno.
 */
class Cliente extends Model
{
    protected $fillable = ['nome', 'email', 'telefone', 'cro'];

    public function agendamentos(): HasMany
    {
        return $this->hasMany(Agendamento::class);
    }
}
