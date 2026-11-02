@extends('layouts.main')

@section('titulo', 'Agendamentos')

@section('content')

<section class="events-section">
    <div class="container">

        <div class="events-section-header">
            <div>
                <span class="section-eyebrow">GESTÃO</span>
                <h2>Agendamentos</h2>
                <p>Atendimentos dos alunos nos cursos do Instituto.</p>
            </div>

            <a href="{{ route('agendamentos.create') }}" class="btn btn-primary create-event-button">
                Novo agendamento
            </a>
        </div>

        @if($agendamentos->isNotEmpty())
            <div class="table-responsive data-table-wrapper">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Data e hora</th>
                            <th scope="col">Aluno</th>
                            <th scope="col">Curso</th>
                            <th scope="col">Status</th>
                            <th scope="col">Pago</th>
                            <th scope="col">Data do pagamento</th>
                            <th scope="col">Forma de pagamento</th>
                            <th scope="col">Registrado por</th>
                            <th scope="col" class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendamentos as $agendamento)
                            <tr>
                                <td class="text-nowrap">{{ $agendamento->data_hora->format('d/m/Y H:i') }}</td>
                                <td>{{ $agendamento->cliente->nome }}</td>
                                <td>{{ $agendamento->evento->titulo }}</td>
                                <td>
                                    <span class="status-badge status-{{ $agendamento->status }}">
                                        {{ \App\Models\Agendamento::STATUS[$agendamento->status] ?? $agendamento->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge {{ $agendamento->pago ? 'status-pago' : 'status-nao-pago' }}">
                                        {{ $agendamento->pago ? 'Sim' : 'Não' }}
                                    </span>
                                </td>
                                <td class="text-nowrap">{{ $agendamento->data_pagamento?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ \App\Models\Agendamento::FORMAS_PAGAMENTO[$agendamento->forma_pagamento] ?? '—' }}</td>
                                <td>{{ $agendamento->responsavel?->name ?? '—' }}</td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('agendamentos.edit', $agendamento) }}" class="btn btn-sm btn-outline-primary">
                                            Editar
                                        </a>

                                        @if(auth()->user()->isAdmin())
                                            <form action="{{ route('agendamentos.destroy', $agendamento) }}" method="POST">
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
                <h3>Nenhum agendamento</h3>
                <p>Crie o primeiro agendamento de um aluno em um curso.</p>
            </div>
        @endif

    </div>
</section>

@endsection
