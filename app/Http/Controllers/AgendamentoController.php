<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Cliente;
use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AgendamentoController extends Controller
{
    public function index()
    {
        return view('agendamentos.index', [
            'agendamentos' => Agendamento::with(['cliente', 'evento', 'responsavel'])->orderBy('data_hora')->get(),
        ]);
    }

    public function create()
    {
        return $this->formulario(new Agendamento(['status' => 'agendado']));
    }

    public function store(Request $request)
    {
        $agendamento = new Agendamento($this->validar($request));
        $agendamento->user_id = $request->user()->id;
        $agendamento->save();

        return redirect()->route('agendamentos.index')->with('msg', 'Agendamento criado com sucesso!');
    }

    public function edit(Agendamento $agendamento)
    {
        return $this->formulario($agendamento);
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        $agendamento->update($this->validar($request, $agendamento));

        return redirect()->route('agendamentos.index')->with('msg', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Agendamento $agendamento)
    {
        $agendamento->delete();

        return redirect()->route('agendamentos.index')->with('msg', 'Agendamento excluído com sucesso!');
    }

    private function formulario(Agendamento $agendamento)
    {
        return view('agendamentos.form', [
            'agendamento' => $agendamento,
            'clientes' => Cliente::orderBy('nome')->get(),
            'eventos' => Evento::orderBy('titulo')->get(),
        ]);
    }

    private function validar(Request $request, ?Agendamento $agendamento = null): array
    {
        $dados = $request->validate([
            'cliente_id' => [
                'required',
                'exists:clientes,id',
                Rule::unique('agendamentos')
                    ->where('evento_id', $request->input('evento_id'))
                    ->ignore($agendamento),
            ],
            'evento_id' => ['required', 'exists:eventos,id'],
            // Datas passadas só são aceitas ao editar um agendamento já existente.
            'data_hora' => $agendamento ? ['required', 'date'] : ['required', 'date', 'after:now'],
            'status' => ['required', Rule::in(array_keys(Agendamento::STATUS))],
            'observacoes' => ['nullable', 'string', 'max:1000'],
            'pago' => ['nullable', 'boolean'],
            'data_pagamento' => [
                'nullable',
                'required_if:pago,1',
                'date',
                'before_or_equal:'.now('America/Bahia')->toDateString(),
            ],
            'forma_pagamento' => [
                'nullable',
                'required_if:pago,1',
                Rule::in(array_keys(Agendamento::FORMAS_PAGAMENTO)),
            ],
        ], [
            'cliente_id.unique' => 'Este aluno já tem agendamento neste curso.',
        ]);

        $dados['pago'] = $request->boolean('pago');

        // Sem pagamento, não ficam data nem forma de um registro anterior.
        if (! $dados['pago']) {
            $dados['data_pagamento'] = null;
            $dados['forma_pagamento'] = null;
        }

        return $dados;
    }
}
