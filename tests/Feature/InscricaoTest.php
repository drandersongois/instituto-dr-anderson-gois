<?php

namespace Tests\Feature;

use App\Mail\InscricaoCurso;
use App\Models\Evento;
use App\Models\Inscricao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class InscricaoTest extends TestCase
{
    use RefreshDatabase;

    private function criarInscricao(Evento $curso, array $alteracoes = []): Inscricao
    {
        return $curso->inscricoes()->create($alteracoes + [
            'cpf' => '52998224725',
        ] + $this->dados());
    }

    private function criarCurso(bool $privado = false, string $titulo = 'Curso de Teste'): Evento
    {
        return Evento::create([
            'titulo' => $titulo,
            'area' => 'Ortodontia',
            'cidade' => 'Feira de Santana - BA',
            'data_inicio' => '2030-01-10',
            'carga_horaria' => 20,
            'valor' => 1500,
            'descricao' => 'Descrição do curso.',
            'privado' => $privado,
        ]);
    }

    private function dados(array $alteracoes = []): array
    {
        return $alteracoes + [
            'nome' => 'Aluno Teste',
            'email' => 'aluno@exemplo.test',
            'telefone' => '(75) 90000-0000',
            'cpf' => '529.982.247-25',
            'data_nascimento' => '1990-05-20',
            'sexo' => 'F',
            'endereco' => 'Rua de Teste, 10, Centro, Feira de Santana - BA',
        ];
    }

    public function test_pagina_do_curso_mostra_formulario_e_rodape_de_contato(): void
    {
        $curso = $this->criarCurso();

        $this->get(route('events.show', $curso))
            ->assertOk()
            ->assertSee('Inscreva-se neste curso')
            ->assertSee(route('inscricoes.store', $curso))
            ->assertSee('@drandersongois')
            ->assertSee('https://www.instagram.com/drandersongois/');
    }

    public function test_telefone_so_aparece_no_rodape_quando_configurado(): void
    {
        config(['instituto.telefone' => null]);
        $this->get('/')->assertDontSee('tel:', false);

        config(['instituto.telefone' => '(75) 3000-0000']);
        $this->get('/')->assertSee('tel:+557530000000', false)->assertSee('(75) 3000-0000');
    }

    public function test_inscricao_envia_email_para_o_instituto(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), $this->dados())
            ->assertRedirect(route('events.show', $curso))
            ->assertSessionHas('msg');

        Mail::assertSent(InscricaoCurso::class, function (InscricaoCurso $email) use ($curso) {
            return $email->hasTo('drandersongois@gmail.com')
                && $email->hasReplyTo('aluno@exemplo.test')
                && $email->hasSubject('Nova inscrição: '.$curso->titulo);
        });
    }

    public function test_email_mostra_os_dados_e_escapa_html(): void
    {
        $html = (new InscricaoCurso($this->criarInscricao($this->criarCurso(), [
            'mensagem' => '<script>alerta</script>',
        ])))->render();

        $this->assertStringContainsString('Aluno Teste', $html);
        $this->assertStringContainsString('(75) 90000-0000', $html);
        $this->assertStringContainsString('529.982.247-25', $html);
        $this->assertStringContainsString('20/05/1990', $html);
        $this->assertStringContainsString('<strong>Sexo:</strong> F', $html);
        $this->assertStringContainsString('Rua de Teste, 10', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_data_da_inscricao_e_automatica_e_nao_vem_do_formulario(): void
    {
        Mail::fake();
        $this->travelTo(Carbon::parse('2026-11-02 15:30:00', 'UTC'));
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), $this->dados([
            'created_at' => '2000-01-01 00:00:00',
        ]));

        $html = (new InscricaoCurso(Inscricao::firstOrFail()))->render();

        $this->assertStringContainsString('02/11/2026 às 12:30', $html);
        $this->assertStringNotContainsString('01/01/2000', $html);
    }

    public function test_inscricao_fica_salva_com_cpf_so_em_digitos(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), $this->dados());

        $this->assertDatabaseHas('inscricoes', [
            'evento_id' => $curso->id,
            'nome' => 'Aluno Teste',
            'cpf' => '52998224725',
        ]);
    }

    public function test_visitante_nao_acessa_a_lista_de_inscritos(): void
    {
        $this->get(route('inscricoes.index'))->assertRedirect(route('login'));
    }

    public function test_equipe_lista_inscritos_e_a_busca_filtra(): void
    {
        $curso = $this->criarCurso();
        $outroCurso = $this->criarCurso(titulo: 'Outro Curso');
        $this->criarInscricao($curso, ['nome' => 'Maria Souza', 'email' => 'maria@exemplo.test']);
        $this->criarInscricao($outroCurso, [
            'nome' => 'Joao Lima',
            'email' => 'joao@exemplo.test',
            'cpf' => '11144477735',
        ]);

        foreach (['admin', 'atendente'] as $perfil) {
            $this->actingAs(User::factory()->create(['perfil' => $perfil]))
                ->get(route('inscricoes.index'))
                ->assertOk()
                ->assertSee('Maria Souza')
                ->assertSee('Joao Lima')
                ->assertSee('529.982.247-25');
        }

        $buscas = ['maria', 'maria@exemplo', '529.982.247-25', '52998224725', 'Curso de Teste'];

        foreach ($buscas as $busca) {
            $this->get(route('inscricoes.index', ['search' => $busca]))
                ->assertOk()
                ->assertSee('Maria Souza')
                ->assertDontSee('Joao Lima');
        }

        $this->get(route('inscricoes.index', ['search' => '%']))
            ->assertOk()
            ->assertSee('Nenhuma inscrição encontrada');
    }

    public function test_curso_com_inscricoes_nao_pode_ser_excluido(): void
    {
        $curso = $this->criarCurso();
        $this->criarInscricao($curso);

        $this->actingAs(User::factory()->create(['perfil' => 'admin']))
            ->delete(route('events.destroy', $curso))
            ->assertSessionHas('erro');

        $this->assertDatabaseHas('eventos', ['id' => $curso->id]);
    }

    public function test_dados_invalidos_nao_enviam_email(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), ['email' => 'invalido'])
            ->assertRedirect(route('events.show', $curso).'#inscricao')
            ->assertSessionHasErrors(['nome', 'email', 'telefone', 'cpf', 'data_nascimento', 'sexo', 'endereco']);

        Mail::assertNothingSent();
    }

    public function test_cpf_e_validado_pelos_digitos_verificadores(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        foreach (['111.111.111-11', '529.982.247-24', '123'] as $cpf) {
            $this->post(route('inscricoes.store', $curso), $this->dados(['cpf' => $cpf]))
                ->assertSessionHasErrors('cpf');
        }

        Mail::assertNothingSent();

        foreach (['529.982.247-25', '52998224725'] as $cpf) {
            $this->post(route('inscricoes.store', $curso), $this->dados(['cpf' => $cpf]))
                ->assertSessionHasNoErrors();
        }
    }

    public function test_data_de_nascimento_e_sexo_sao_validados(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), $this->dados([
            'data_nascimento' => now()->addDay()->format('Y-m-d'),
            'sexo' => 'X',
        ]))->assertSessionHasErrors(['data_nascimento', 'sexo']);

        Mail::assertNothingSent();
    }

    public function test_falha_no_email_nao_perde_a_inscricao(): void
    {
        $this->withoutExceptionHandling();
        $curso = $this->criarCurso();
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP fora do ar'));

        $this->post(route('inscricoes.store', $curso), $this->dados())
            ->assertRedirect(route('events.show', $curso))
            ->assertSessionHas('msg');

        $this->assertDatabaseCount('inscricoes', 1);
    }

    public function test_campo_invisivel_preenchido_nao_envia_email(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), $this->dados(['empresa' => 'robo']))
            ->assertRedirect(route('events.show', $curso));

        Mail::assertNothingSent();
    }

    public function test_visitante_nao_se_inscreve_em_curso_privado(): void
    {
        Mail::fake();
        $curso = $this->criarCurso(privado: true);

        $this->post(route('inscricoes.store', $curso), $this->dados())->assertNotFound();

        Mail::assertNothingSent();
    }
}
