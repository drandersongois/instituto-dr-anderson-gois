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
