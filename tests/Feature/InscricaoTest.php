<?php

namespace Tests\Feature;

use App\Mail\InscricaoCurso;
use App\Models\Evento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use RuntimeException;
use Tests\TestCase;

class InscricaoTest extends TestCase
{
    use RefreshDatabase;

    private function criarCurso(bool $privado = false): Evento
    {
        return Evento::create([
            'titulo' => 'Curso de Teste',
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
        $html = (new InscricaoCurso($this->criarCurso(), $this->dados([
            'mensagem' => '<script>alerta</script>',
        ])))->render();

        $this->assertStringContainsString('Aluno Teste', $html);
        $this->assertStringContainsString('(75) 90000-0000', $html);
        $this->assertStringNotContainsString('<script>', $html);
    }

    public function test_dados_invalidos_nao_enviam_email(): void
    {
        Mail::fake();
        $curso = $this->criarCurso();

        $this->post(route('inscricoes.store', $curso), ['email' => 'invalido'])
            ->assertRedirect(route('events.show', $curso).'#inscricao')
            ->assertSessionHasErrors(['nome', 'email', 'telefone']);

        Mail::assertNothingSent();
    }

    public function test_falha_no_envio_avisa_o_visitante_e_preserva_os_dados(): void
    {
        $curso = $this->criarCurso();
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP fora do ar'));

        $this->post(route('inscricoes.store', $curso), $this->dados())
            ->assertRedirect(route('events.show', $curso).'#inscricao')
            ->assertSessionHas('erro')
            ->assertSessionMissing('msg')
            ->assertSessionHasInput('nome', 'Aluno Teste');
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
