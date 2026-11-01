<?php

namespace Tests\Feature;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InstitutoTest extends TestCase
{
    use RefreshDatabase;

    private function curso(array $dados = []): array
    {
        return $dados + [
            'titulo' => 'Curso de Teste',
            'area' => 'Ortodontia',
            'cidade' => 'São Paulo',
            'data_inicio' => '2030-01-10',
            'carga_horaria' => 20,
            'valor' => 1500,
            'descricao' => 'Descrição do curso.',
            'privado' => 0,
        ];
    }

    private function criarCurso(array $dados = []): Evento
    {
        return Evento::create($this->curso($dados));
    }

    private function criarCliente(): Cliente
    {
        return Cliente::create([
            'nome' => 'Aluno Teste',
            'email' => 'aluno@exemplo.test',
            'telefone' => '(11) 90000-0000',
        ]);
    }

    public function test_seeder_cria_os_quatro_cursos_e_pode_rodar_duas_vezes(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(4, Evento::count());
        $this->assertSame(3, Agendamento::count());
        $this->get('/')
            ->assertOk()
            ->assertSee('Ortodontia')
            ->assertSee('DTM e Bruxismo')
            ->assertSee('Estética')
            ->assertSee('Harmonização')
            ->assertSee('Instituto Dr Anderson Gois');
    }

    public function test_login_e_logout_funcionam_e_senha_fica_em_hash(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('usuario', 'anderson')->first();

        $this->assertTrue($admin->isAdmin());
        $this->assertNotSame('12345678', $admin->password);
        $this->assertTrue(Hash::check('12345678', $admin->password));

        $this->post('/login', ['usuario' => 'anderson', 'password' => 'errada'])
            ->assertSessionHasErrors('usuario');
        $this->assertGuest();

        $this->post('/login', ['usuario' => 'anderson', 'password' => '12345678'])
            ->assertRedirect(route('agendamentos.index'));
        $this->assertAuthenticatedAs($admin);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_visitante_e_redirecionado_ao_login_nas_paginas_protegidas(): void
    {
        foreach (['/clientes', '/clientes/create', '/agendamentos', '/agendamentos/create', '/events/create'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }

        $this->post('/events', $this->curso())->assertRedirect(route('login'));
        $this->assertSame(0, Evento::count());
    }

    public function test_atendente_nao_gerencia_cursos_nem_exclui(): void
    {
        $atendente = User::factory()->create();
        $curso = $this->criarCurso();
        $cliente = $this->criarCliente();
        $agendamento = Agendamento::create([
            'cliente_id' => $cliente->id,
            'evento_id' => $curso->id,
            'data_hora' => '2030-01-10 08:00:00',
        ]);

        $this->actingAs($atendente);

        $this->get('/clientes')->assertOk();
        $this->get('/agendamentos')->assertOk();
        $this->get('/events/create')->assertForbidden();
        $this->post('/events', $this->curso())->assertForbidden();
        $this->put("/events/{$curso->id}", $this->curso())->assertForbidden();
        $this->delete("/events/{$curso->id}")->assertForbidden();
        $this->delete("/clientes/{$cliente->id}")->assertForbidden();
        $this->delete("/agendamentos/{$agendamento->id}")->assertForbidden();

        $this->assertDatabaseCount('agendamentos', 1);
    }

    public function test_admin_faz_crud_completo_de_cursos(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->post('/events', $this->curso())->assertRedirect(route('home'));
        $curso = Evento::firstOrFail();

        $this->get("/events/{$curso->id}")->assertOk()->assertSee('Curso de Teste');
        $this->get("/events/{$curso->id}/edit")->assertOk();

        $this->put("/events/{$curso->id}", $this->curso(['titulo' => 'Curso Alterado']))
            ->assertRedirect(route('events.show', $curso));
        $this->assertDatabaseHas('eventos', ['id' => $curso->id, 'titulo' => 'Curso Alterado']);

        $this->delete("/events/{$curso->id}")->assertRedirect(route('home'));
        $this->assertDatabaseCount('eventos', 0);
    }

    public function test_curso_sem_imagem_usa_a_capa_padrao(): void
    {
        $this->actingAs(User::factory()->admin()->create());
        $this->post('/events', $this->curso())->assertSessionHasNoErrors();

        $this->get('/')->assertSee('img/eventos_img/curso-padrao.svg');
    }

    public function test_dados_invalidos_de_curso_sao_rejeitados(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->from('/events/create')
            ->post('/events', ['titulo' => '', 'area' => 'Outra', 'valor' => -1])
            ->assertRedirect('/events/create')
            ->assertSessionHasErrors(['titulo', 'area', 'cidade', 'valor', 'descricao']);

        $this->assertDatabaseCount('eventos', 0);
    }

    public function test_curso_privado_fica_oculto_para_visitante(): void
    {
        $curso = $this->criarCurso(['titulo' => 'Curso Fechado', 'privado' => 1]);

        $this->get('/')->assertDontSee('Curso Fechado');
        $this->get("/events/{$curso->id}")->assertNotFound();

        $this->actingAs(User::factory()->create());
        $this->get('/')->assertSee('Curso Fechado');
        $this->get("/events/{$curso->id}")->assertOk();
    }

    public function test_busca_filtra_por_titulo_e_area(): void
    {
        $this->criarCurso(['titulo' => 'Curso A', 'area' => 'Ortodontia']);
        $this->criarCurso(['titulo' => 'Curso B', 'area' => 'Harmonização']);

        $this->get('/?search=Harmoniza')->assertSee('Curso B')->assertDontSee('Curso A');
        $this->get('/?search=Curso+A')->assertSee('Curso A')->assertDontSee('Curso B');
    }

    public function test_atendente_cria_agendamento_e_duplicado_e_recusado(): void
    {
        $atendente = User::factory()->create();
        $curso = $this->criarCurso();
        $cliente = $this->criarCliente();
        $dados = [
            'cliente_id' => $cliente->id,
            'evento_id' => $curso->id,
            'data_hora' => now()->addDay()->format('Y-m-d\TH:i'),
            'status' => 'agendado',
        ];

        $this->actingAs($atendente);

        $this->post('/agendamentos', $dados)->assertRedirect(route('agendamentos.index'));
        $this->assertDatabaseHas('agendamentos', ['cliente_id' => $cliente->id, 'user_id' => $atendente->id]);
        $this->get('/agendamentos')->assertSee('Aluno Teste')->assertSee('Curso de Teste');

        $this->post('/agendamentos', $dados)->assertSessionHasErrors('cliente_id');
        $this->assertDatabaseCount('agendamentos', 1);

        $agendamento = Agendamento::firstOrFail();
        $this->put("/agendamentos/{$agendamento->id}", ['status' => 'confirmado'] + $dados)
            ->assertRedirect(route('agendamentos.index'));
        $this->assertSame('confirmado', $agendamento->fresh()->status);
    }

    public function test_agendamento_no_passado_e_recusado(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post('/agendamentos', [
            'cliente_id' => $this->criarCliente()->id,
            'evento_id' => $this->criarCurso()->id,
            'data_hora' => now()->subDay()->format('Y-m-d\TH:i'),
            'status' => 'agendado',
        ])->assertSessionHasErrors('data_hora');
    }

    public function test_aluno_e_curso_com_agendamento_nao_podem_ser_excluidos(): void
    {
        $curso = $this->criarCurso();
        $cliente = $this->criarCliente();
        Agendamento::create([
            'cliente_id' => $cliente->id,
            'evento_id' => $curso->id,
            'data_hora' => '2030-01-10 08:00:00',
        ]);

        $this->actingAs(User::factory()->admin()->create());

        $this->delete("/clientes/{$cliente->id}")->assertSessionHas('erro');
        $this->delete("/events/{$curso->id}")->assertSessionHas('erro');

        $this->assertDatabaseCount('clientes', 1);
        $this->assertDatabaseCount('eventos', 1);
    }
}
