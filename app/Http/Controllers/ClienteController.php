<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        return view('clientes.index', [
            'clientes' => Cliente::withCount('agendamentos')->orderBy('nome')->get(),
        ]);
    }

    public function create()
    {
        return view('clientes.form', ['cliente' => new Cliente]);
    }

    public function store(Request $request)
    {
        Cliente::create($this->validar($request));

        return redirect()->route('clientes.index')->with('msg', 'Aluno cadastrado com sucesso!');
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', ['cliente' => $cliente]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $cliente->update($this->validar($request, $cliente));

        return redirect()->route('clientes.index')->with('msg', 'Aluno atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->agendamentos()->exists()) {
            return back()->with('erro', 'Este aluno tem agendamentos e não pode ser excluído.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('msg', 'Aluno excluído com sucesso!');
    }

    private function validar(Request $request, ?Cliente $cliente = null): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clientes')->ignore($cliente)],
            'telefone' => ['required', 'string', 'max:20'],
            'cro' => ['nullable', 'string', 'max:20'],
        ]);
    }
}
