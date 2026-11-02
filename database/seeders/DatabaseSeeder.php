<?php

namespace Database\Seeders;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Dados de exemplo para demonstração do sistema.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['usuario' => 'anderson'],
            ['name' => 'Dr Anderson Gois', 'email' => 'anderson@instituto.test', 'password' => '12345678', 'perfil' => 'admin']
        );

        User::updateOrCreate(
            ['usuario' => 'atendente'],
            ['name' => 'Atendente', 'email' => 'atendente@instituto.test', 'password' => '12345678', 'perfil' => 'atendente']
        );

        $cursos = [
            [
                'titulo' => 'Ortodontia Clínica: do Diagnóstico à Finalização',
                'area' => 'Ortodontia',
                'cidade' => 'São Paulo',
                'data_inicio' => '2027-03-13',
                'carga_horaria' => 120,
                'valor' => 4800,
                'imagem' => 'ortodontia.png',
                'descricao' => "Formação prática para o cirurgião-dentista que deseja conduzir casos ortodônticos com segurança, do planejamento à contenção.\n\nO curso aborda documentação e diagnóstico, cefalometria, biomecânica, colagem de bráquetes e sequência de fios, com discussão de casos reais e atividades práticas em manequim.",
            ],
            [
                'titulo' => 'DTM e Bruxismo: Diagnóstico e Manejo Clínico',
                'area' => 'DTM e Bruxismo',
                'cidade' => 'São Paulo',
                'data_inicio' => '2027-04-10',
                'carga_horaria' => 40,
                'valor' => 2200,
                'imagem' => 'dtm-bruxismo.jpg',
                'descricao' => "Um curso para reconhecer, diagnosticar e acompanhar pacientes com disfunção temporomandibular e bruxismo.\n\nO conteúdo inclui anatomia da ATM, anamnese e exame físico, diagnóstico diferencial da dor orofacial, confecção e ajuste de placas oclusais, abordagem multidisciplinar e protocolos de acompanhamento.",
            ],
            [
                'titulo' => 'Estética do Sorriso: Resinas e Planejamento Digital',
                'area' => 'Estética',
                'cidade' => 'Rio de Janeiro',
                'data_inicio' => '2027-05-15',
                'carga_horaria' => 48,
                'valor' => 2900,
                'imagem' => 'estetica.webp',
                'descricao' => "Do planejamento à entrega, este curso reúne os principais recursos da odontologia estética restauradora.\n\nAs aulas abordam análise facial e dental, fotografia odontológica, enceramento e mock-up, clareamento, facetas diretas em resina composta, acabamento e polimento, com prática supervisionada.",
            ],
            [
                'titulo' => 'Harmonização Orofacial: Fundamentos e Prática',
                'area' => 'Harmonização',
                'cidade' => 'Belo Horizonte',
                'data_inicio' => '2027-06-12',
                'carga_horaria' => 60,
                'valor' => 3600,
                'imagem' => 'harmonizacao.jpg',
                'descricao' => "Introdução responsável à harmonização orofacial para cirurgiões-dentistas, com foco em anatomia e segurança.\n\nO programa abrange anatomia aplicada da face, avaliação e planejamento, toxina botulínica, preenchedores à base de ácido hialurônico, biossegurança e conduta diante de intercorrências.",
            ],
        ];

        foreach ($cursos as $curso) {
            $evento = Evento::firstOrNew(['titulo' => $curso['titulo']]);
            $evento->fill($curso + ['privado' => false]);
            $evento->imagem = $curso['imagem'];
            $evento->save();
        }

        $alunos = [
            ['nome' => 'Mariana Souza', 'email' => 'mariana.souza@exemplo.test', 'telefone' => '(11) 90000-0001', 'cro' => 'CRO-SP 00001'],
            ['nome' => 'Rafael Lima', 'email' => 'rafael.lima@exemplo.test', 'telefone' => '(21) 90000-0002', 'cro' => 'CRO-RJ 00002'],
            ['nome' => 'Camila Rocha', 'email' => 'camila.rocha@exemplo.test', 'telefone' => '(31) 90000-0003', 'cro' => 'CRO-MG 00003'],
        ];

        foreach ($alunos as $aluno) {
            Cliente::updateOrCreate(['email' => $aluno['email']], $aluno);
        }

        $agendamentos = [
            ['mariana.souza@exemplo.test', 'Ortodontia', '2027-03-13 08:30:00', 'confirmado'],
            ['rafael.lima@exemplo.test', 'DTM e Bruxismo', '2027-04-10 08:30:00', 'agendado'],
            ['camila.rocha@exemplo.test', 'Harmonização', '2027-06-12 09:00:00', 'agendado'],
        ];

        foreach ($agendamentos as [$email, $area, $dataHora, $status]) {
            $agendamento = Agendamento::firstOrNew([
                'cliente_id' => Cliente::where('email', $email)->value('id'),
                'evento_id' => Evento::where('area', $area)->value('id'),
            ]);
            $agendamento->fill(['data_hora' => $dataHora, 'status' => $status]);
            $agendamento->user_id = $admin->id;
            $agendamento->save();
        }
    }
}
