@extends('layouts.main')

@section('titulo', $evento->titulo)

@section('content')

<section class="event-show-page">
    <div class="container">

        <a href="{{ route('home') }}" class="event-show-back">&larr; Todos os cursos</a>

        <div class="row g-5 mt-0">

            <div class="col-lg-7">
                <img
                    src="{{ $evento->imagemUrl() }}"
                    alt="Capa do curso {{ $evento->titulo }}"
                    class="event-show-image"
                >

                <h2 class="event-show-subtitle">Sobre o curso</h2>
                <p class="event-show-description">{{ $evento->descricao }}</p>

                <div class="event-show-instructor">
                    <img
                        src="{{ asset('img/dr-anderson-gois-consultorio.jpg') }}"
                        alt="Dr Anderson Gois em seu consultório"
                    >

                    <div>
                        <strong>Dr Anderson Gois</strong>
                        <span>Coordenação do curso</span>
                    </div>
                </div>

                <div class="event-signup" id="inscricao">
                    <h2 class="event-show-subtitle">Inscreva-se neste curso</h2>

                    <form action="{{ route('inscricoes.store', $evento) }}" method="POST">
                        @csrf

                        <div class="event-signup-trap" aria-hidden="true">
                            <label for="empresa">Empresa</label>
                            <input type="text" id="empresa" name="empresa" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label for="nome" class="form-label">Nome completo</label>
                                <input
                                    type="text"
                                    class="form-control @error('nome') is-invalid @enderror"
                                    id="nome"
                                    name="nome"
                                    value="{{ old('nome') }}"
                                    maxlength="255"
                                    autocomplete="name"
                                    required
                                >
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input
                                    type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    maxlength="255"
                                    autocomplete="email"
                                    required
                                >
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefone" class="form-label">Telefone</label>
                                <input
                                    type="tel"
                                    class="form-control @error('telefone') is-invalid @enderror"
                                    id="telefone"
                                    name="telefone"
                                    value="{{ old('telefone') }}"
                                    maxlength="30"
                                    autocomplete="tel"
                                    required
                                >
                                @error('telefone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="cro" class="form-label">CRO (opcional)</label>
                                <input
                                    type="text"
                                    class="form-control @error('cro') is-invalid @enderror"
                                    id="cro"
                                    name="cro"
                                    value="{{ old('cro') }}"
                                    maxlength="30"
                                >
                                @error('cro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="mensagem" class="form-label">Mensagem (opcional)</label>
                                <textarea
                                    class="form-control @error('mensagem') is-invalid @enderror"
                                    id="mensagem"
                                    name="mensagem"
                                    rows="4"
                                    maxlength="2000"
                                >{{ old('mensagem') }}</textarea>
                                @error('mensagem')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <p class="event-signup-note">
                                    Seus dados serão usados apenas para o contato sobre esta inscrição.
                                </p>
                                <button type="submit" class="btn btn-primary">Enviar inscrição</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <aside class="event-show-card">
                    @if($evento->area)
                        <span class="section-eyebrow">{{ mb_strtoupper($evento->area) }}</span>
                    @endif

                    <h1>{{ $evento->titulo }}</h1>

                    <dl class="event-show-details">
                        <dt>Cidade</dt>
                        <dd>{{ $evento->cidade }}</dd>

                        @if($evento->data_inicio)
                            <dt>Início</dt>
                            <dd>{{ $evento->data_inicio->format('d/m/Y') }}</dd>
                        @endif

                        @if($evento->carga_horaria)
                            <dt>Carga horária</dt>
                            <dd>{{ $evento->carga_horaria }} horas</dd>
                        @endif

                        @if($evento->valor !== null)
                            <dt>Investimento</dt>
                            <dd>R$ {{ number_format((float) $evento->valor, 2, ',', '.') }}</dd>
                        @endif
                    </dl>

                    <a href="#inscricao" class="btn btn-primary w-100 mt-4">Quero me inscrever</a>

                    @if($evento->privado)
                        <p class="event-show-private">Curso privado: visível só para a equipe.</p>
                    @endif

                    @if(auth()->user()?->isAdmin())
                        <div class="event-show-actions">
                            <a href="{{ route('events.edit', $evento) }}" class="btn btn-primary">
                                Editar curso
                            </a>

                            <form action="{{ route('events.destroy', $evento) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    Excluir curso
                                </button>
                            </form>
                        </div>
                    @endif
                </aside>
            </div>

        </div>

    </div>
</section>

@endsection
