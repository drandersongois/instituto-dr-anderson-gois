@extends('layouts.main')

@section('titulo', 'Inscrições')

@section('content')

<section class="events-section">
    <div class="container">

        <div class="events-section-header">
            <div>
                <span class="section-eyebrow">GESTÃO</span>
                <h2>Alunos inscritos</h2>
                <p>Inscrições recebidas pelo formulário dos cursos.</p>
            </div>
        </div>

        <form action="{{ route('inscricoes.index') }}" method="GET" role="search" class="mb-4">
            <label for="search" class="form-label">Buscar inscrição</label>
            <div class="input-group">
                <input
                    type="search"
                    class="form-control"
                    id="search"
                    name="search"
                    value="{{ $busca }}"
                    placeholder="Nome, e-mail, telefone, CPF ou curso"
                >
                <button type="submit" class="btn btn-primary">Buscar</button>
            </div>
        </form>

        @if($inscricoes->isNotEmpty())
            <div class="table-responsive data-table-wrapper">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Inscrição</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Curso</th>
                            <th scope="col">CPF</th>
                            <th scope="col">Nascimento</th>
                            <th scope="col">Sexo</th>
                            <th scope="col">Telefone</th>
                            <th scope="col">E-mail</th>
                            <th scope="col">Endereço</th>
                            <th scope="col">CRO</th>
                            <th scope="col">Mensagem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inscricoes as $inscricao)
                            <tr>
                                <td class="text-nowrap">{{ $inscricao->dataInscricao()->format('d/m/Y H:i') }}</td>
                                <td>{{ $inscricao->nome }}</td>
                                <td>{{ $inscricao->evento->titulo }}</td>
                                <td class="text-nowrap">{{ $inscricao->cpfFormatado() }}</td>
                                <td class="text-nowrap">{{ $inscricao->data_nascimento->format('d/m/Y') }}</td>
                                <td>{{ $inscricao->sexo }}</td>
                                <td class="text-nowrap">{{ $inscricao->telefone }}</td>
                                <td>{{ $inscricao->email }}</td>
                                <td>{{ $inscricao->endereco }}</td>
                                <td>{{ $inscricao->cro ?: '—' }}</td>
                                <td>{{ $inscricao->mensagem ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $inscricoes->links('pagination::bootstrap-5') }}
            </div>
        @else
            <div class="events-empty-state">
                @if($busca !== '')
                    <h3>Nenhuma inscrição encontrada</h3>
                    <p>Tente buscar por outro termo.</p>
                @else
                    <h3>Nenhuma inscrição recebida</h3>
                    <p>As inscrições feitas nas páginas dos cursos aparecerão aqui.</p>
                @endif
            </div>
        @endif

    </div>
</section>

@endsection
