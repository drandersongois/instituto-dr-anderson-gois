@extends('layouts.main')

@section('titulo', $cliente->exists ? 'Editar aluno' : 'Novo aluno')

@section('content')

<section class="event-create-page">
    <div class="container">
        <div class="event-create-wrapper">

            <div class="event-create-header">
                <span class="section-eyebrow">GESTÃO</span>
                <h1>{{ $cliente->exists ? 'Editar aluno' : 'Novo aluno' }}</h1>
            </div>

            <div class="event-create-card">
                <form
                    action="{{ $cliente->exists ? route('clientes.update', $cliente) : route('clientes.store') }}"
                    method="POST"
                    class="event-create-form"
                >
                    @csrf

                    @if($cliente->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label for="nome" class="form-label">Nome completo</label>
                        <input
                            type="text"
                            class="form-control @error('nome') is-invalid @enderror"
                            id="nome"
                            name="nome"
                            value="{{ old('nome', $cliente->nome) }}"
                            maxlength="255"
                            required
                        >
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">E-mail</label>
                        <input
                            type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email', $cliente->email) }}"
                            maxlength="255"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input
                                type="tel"
                                class="form-control @error('telefone') is-invalid @enderror"
                                id="telefone"
                                name="telefone"
                                value="{{ old('telefone', $cliente->telefone) }}"
                                placeholder="(11) 90000-0000"
                                maxlength="20"
                                required
                            >
                            @error('telefone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="cro" class="form-label">CRO (opcional)</label>
                            <input
                                type="text"
                                class="form-control @error('cro') is-invalid @enderror"
                                id="cro"
                                name="cro"
                                value="{{ old('cro', $cliente->cro) }}"
                                maxlength="20"
                            >
                            @error('cro')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="event-create-actions">
                        <a href="{{ route('clientes.index') }}" class="btn btn-light cancel-button">Cancelar</a>
                        <button type="submit" class="btn btn-primary submit-button">
                            {{ $cliente->exists ? 'Salvar alterações' : 'Cadastrar aluno' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

@endsection
