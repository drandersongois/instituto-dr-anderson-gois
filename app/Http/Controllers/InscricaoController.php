<?php

namespace App\Http\Controllers;

use App\Mail\InscricaoCurso;
use App\Models\Evento;
use App\Models\Inscricao;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class InscricaoController extends Controller
{
    public const SEXOS = ['M', 'F', 'Outros'];

    public function store(Request $request, Evento $evento)
    {
        abort_if($evento->privado && ! $request->user(), 404);

        // Campo invisível para pessoas: quando vem preenchido, o envio é de um robô.
        if ($request->filled('empresa')) {
            return $this->sucesso($evento);
        }

        $paginaDoFormulario = route('events.show', $evento).'#inscricao';

        $validator = Validator::make($request->all(), [
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'telefone' => ['required', 'string', 'max:30'],
            'cpf' => ['required', 'string', 'max:14', function (string $campo, mixed $valor, Closure $falhar) {
                if (! is_string($valor) || ! $this->cpfValido($valor)) {
                    $falhar('Informe um CPF válido.');
                }
            }],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'sexo' => ['required', Rule::in(self::SEXOS)],
            'endereco' => ['required', 'string', 'max:500'],
            'cro' => ['nullable', 'string', 'max:30'],
            'mensagem' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return redirect($paginaDoFormulario)->withErrors($validator)->withInput();
        }

        $dados = $validator->validated();
        $dados['cpf'] = preg_replace('/\D/', '', $dados['cpf']);

        $inscricao = $evento->inscricoes()->create($dados);

        // A inscrição já está salva: uma falha no e-mail não deve fazer o aluno repetir o envio.
        try {
            Mail::to(config('instituto.email_inscricoes'))->send(new InscricaoCurso($inscricao));
        } catch (Throwable $erro) {
            report($erro);
        }

        return $this->sucesso($evento);
    }

    public function index(Request $request)
    {
        $busca = trim((string) $request->query('search'));

        $inscricoes = Inscricao::query()
            ->with('evento')
            ->when($busca !== '', function ($query) use ($busca) {
                $termo = '%'.addcslashes($busca, '%_\\').'%';
                $digitos = preg_replace('/\D/', '', $busca);

                $query->where(function ($query) use ($termo, $digitos) {
                    $query->where('nome', 'like', $termo)
                        ->orWhere('email', 'like', $termo)
                        ->orWhere('telefone', 'like', $termo)
                        ->orWhereHas('evento', fn ($curso) => $curso->where('titulo', 'like', $termo))
                        ->when($digitos !== '', fn ($query) => $query->orWhere('cpf', 'like', "%{$digitos}%"));
                });
            })
            ->latest()
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return view('inscricoes.index', ['inscricoes' => $inscricoes, 'busca' => $busca]);
    }

    private function cpfValido(string $cpf): bool
    {
        if (! preg_match('/^\d{3}\.?\d{3}\.?\d{3}-?\d{2}$/', $cpf)) {
            return false;
        }

        $digitos = preg_replace('/\D/', '', $cpf);

        if (preg_match('/^(\d)\1{10}$/', $digitos)) {
            return false;
        }

        foreach ([9, 10] as $tamanho) {
            $soma = 0;

            for ($i = 0; $i < $tamanho; $i++) {
                $soma += (int) $digitos[$i] * ($tamanho + 1 - $i);
            }

            if ((int) $digitos[$tamanho] !== (10 * $soma) % 11 % 10) {
                return false;
            }
        }

        return true;
    }

    private function sucesso(Evento $evento)
    {
        return redirect()
            ->route('events.show', $evento)
            ->with('msg', 'Inscrição enviada com sucesso! Entraremos em contato em breve.');
    }
}
