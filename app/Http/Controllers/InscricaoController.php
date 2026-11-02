<?php

namespace App\Http\Controllers;

use App\Mail\InscricaoCurso;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class InscricaoController extends Controller
{
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
            'cro' => ['nullable', 'string', 'max:30'],
            'mensagem' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($validator->fails()) {
            return redirect($paginaDoFormulario)->withErrors($validator)->withInput();
        }

        try {
            Mail::to(config('instituto.email_inscricoes'))
                ->send(new InscricaoCurso($evento, $validator->validated()));
        } catch (Throwable $erro) {
            report($erro);

            return redirect($paginaDoFormulario)
                ->with('erro', 'Não foi possível enviar sua inscrição. Tente novamente em instantes.')
                ->withInput();
        }

        return $this->sucesso($evento);
    }

    private function sucesso(Evento $evento)
    {
        return redirect()
            ->route('events.show', $evento)
            ->with('msg', 'Inscrição enviada com sucesso! Entraremos em contato em breve.');
    }
}
