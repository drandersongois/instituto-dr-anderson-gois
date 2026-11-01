@extends('layouts.main')

@section('titulo', 'Alunos')

@section('content')

<section class="events-section">
    <div class="container">

        <div class="events-section-header">
            <div>
                <span class="section-eyebrow">GESTÃO</span>
                <h2>Alunos</h2>
                <p>Profissionais cadastrados no Instituto.</p>
            </div>

            <a href="{{ route('clientes.create') }}" class="btn btn-primary create-event-button">
                Novo aluno
            </a>
        </div>

        @if($clientes->isNotEmpty())
            <div class="table-responsive data-table-wrapper">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Nome</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">CRO</th>
                            <th scope="col">Agendamentos</th>
                            <th scope="col" class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                            <tr>
                                <td>{{ $cliente->nome }}</td>
                                <td>{{ $cliente->email }}</td>
                                <td>{{ $cliente->telefone }}</td>
                                <td>{{ $cliente->cro ?: '—' }}</td>
                                <td>{{ $cliente->agendamentos_count }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-outline-primary">
                                            Editar
                                        </a>

                                        @if(auth()->user()->isAdmin())
                                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="events-empty-state">
                <h3>Nenhum aluno cadastrado</h3>
                <p>Cadastre o primeiro aluno para criar agendamentos.</p>
            </div>
        @endif

    </div>
</section>

@endsection
