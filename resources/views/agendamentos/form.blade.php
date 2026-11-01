@extends('layouts.main')

@section('titulo', $agendamento->exists ? 'Editar agendamento' : 'Novo agendamento')

@section('content')

<section class="event-create-page">
    <div class="container">
        <div class="event-create-wrapper">

            <div class="event-create-header">
                <span class="section-eyebrow">GESTÃO</span>
                <h1>{{ $agendamento->exists ? 'Editar agendamento' : 'Novo agendamento' }}</h1>
            </div>

            <div class="event-create-card">
                <form
                    action="{{ $agendamento->exists ? route('agendamentos.update', $agendamento) : route('agendamentos.store') }}"
                    method="POST"
                    class="event-create-form"
                >
                    @csrf

                    @if($agendamento->exists)
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label for="cliente_id" class="form-label">Aluno</label>
                        <select
                            name="cliente_id"
                            id="cliente_id"
                            class="form-select @error('cliente_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione</option>
                            @foreach($clientes as $cliente)
                                <option
                                    value="{{ $cliente->id }}"
                                    @selected((string) old('cliente_id', $agendamento->cliente_id) === (string) $cliente->id)
                                >
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="evento_id" class="form-label">Curso</label>
                        <select
                            name="evento_id"
                            id="evento_id"
                            class="form-select @error('evento_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione</option>
                            @foreach($eventos as $evento)
                                <option
                                    value="{{ $evento->id }}"
                                    @selected((string) old('evento_id', $agendamento->evento_id) === (string) $evento->id)
                                >
                                    {{ $evento->titulo }}
                                </option>
                            @endforeach
                        </select>
                        @error('evento_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="data_hora" class="form-label">Data e hora</label>
                            <input
                                type="datetime-local"
                                class="form-control @error('data_hora') is-invalid @enderror"
                                id="data_hora"
                                name="data_hora"
                                value="{{ old('data_hora', $agendamento->data_hora?->format('Y-m-d\TH:i')) }}"
                                required
                            >
                            @error('data_hora')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-4">
                            <label for="status" class="form-label">Status</label>
                            <select
                                name="status"
                                id="status"
                                class="form-select @error('status') is-invalid @enderror"
                                required
                            >
                                @foreach(\App\Models\Agendamento::STATUS as $valor => $rotulo)
                                    <option value="{{ $valor }}" @selected(old('status', $agendamento->status) === $valor)>
                                        {{ $rotulo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="observacoes" class="form-label">Observações (opcional)</label>
                        <textarea
                            class="form-control @error('observacoes') is-invalid @enderror"
                            id="observacoes"
                            name="observacoes"
                            rows="3"
                            maxlength="1000"
                        >{{ old('observacoes', $agendamento->observacoes) }}</textarea>
                        @error('observacoes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="event-create-actions">
                        <a href="{{ route('agendamentos.index') }}" class="btn btn-light cancel-button">Cancelar</a>
                        <button type="submit" class="btn btn-primary submit-button">
                            {{ $agendamento->exists ? 'Salvar alterações' : 'Criar agendamento' }}
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</section>

@endsection
