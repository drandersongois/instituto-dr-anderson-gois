<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagamentoAgendamentoTest extends TestCase
{
    use RefreshDatabase;

    private function dados(array $alteracoes = []): array
    {
        $curso = Evento::create([
            'titulo' => 'Curso de Teste',
            'area' => 'Ortodontia',
            'cidade' => 'Feira de Santana - BA',
            'data_inicio' => '2030-01-10',
            'carga_horaria' => 20,
            'valor' => 1500,
            'descricao' => 'Descrição do curso.',
            'privado' => false,
        ]);

        $aluno = Cliente::create([
            'nome' => 'Aluno Teste',
            'email' => 'aluno@exemplo.test',
            'telefone' => '(75) 90000-0000',
        ]);

        return $alteracoes + [
            'cliente_id' => $aluno->id,
            'evento_id' => $curso->id,
            'data_hora' => '2030-01-10 08:00:00',
            'status' => 'agendado',
        ];
    }

    public function test_registra_pagamento_e_mostra_na_lista(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/agendamentos', $this->dados([
            'pago' => '1',
            'data_pagamento' => '2026-01-15',
            'forma_pagamento' => 'pix',
        ]))->assertRedirect(route('agendamentos.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('agendamentos', ['pago' => true, 'forma_pagamento' => 'pix']);

        $this->get('/agendamentos')
            ->assertOk()
            ->assertSee('Sim')
            ->assertSee('15/01/2026')
            ->assertSee('Pix');
    }

    public function test_pago_exige_data_e_forma_de_pagamento(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/agendamentos', $this->dados(['pago' => '1']))
            ->assertSessionHasErrors(['data_pagamento', 'forma_pagamento']);

        $this->assertDatabaseCount('agendamentos', 0);
    }

    public function test_recusa_data_futura_e_forma_desconhecida(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/agendamentos', $this->dados([
            'pago' => '1',
            'data_pagamento' => now('America/Bahia')->addDay()->toDateString(),
            'forma_pagamento' => 'boleto',
        ]))->assertSessionHasErrors(['data_pagamento', 'forma_pagamento']);
    }

    public function test_marcar_como_nao_pago_limpa_data_e_forma(): void
    {
        $this->actingAs(User::factory()->create());
        $dados = $this->dados();

        $agendamento = Agendamento::create($dados + [
            'pago' => true,
            'data_pagamento' => '2026-01-15',
            'forma_pagamento' => 'dinheiro',
        ]);

        $this->put("/agendamentos/{$agendamento->id}", $dados + [
            'pago' => '0',
            'data_pagamento' => '2026-01-15',
            'forma_pagamento' => 'dinheiro',
        ])->assertSessionHasNoErrors();

        $agendamento->refresh();

        $this->assertFalse($agendamento->pago);
        $this->assertNull($agendamento->data_pagamento);
        $this->assertNull($agendamento->forma_pagamento);
    }

    public function test_agendamento_comeca_como_nao_pago(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/agendamentos', $this->dados())->assertSessionHasNoErrors();

        $this->assertDatabaseHas('agendamentos', ['pago' => false, 'data_pagamento' => null]);
        $this->get('/agendamentos/create')
            ->assertOk()
            ->assertSee('Forma de pagamento')
            ->assertSee('Cartão');
    }
}
