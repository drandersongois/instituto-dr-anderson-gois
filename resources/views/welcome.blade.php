
@extends('layouts.main')

@section('titulo', 'Cursos')

@section('content')

<section class="events-page">

    <!-- Apresentação e busca de cursos -->
    <section class="event-search">
        <div class="container">
            <div class="event-search-content">
                <span class="section-eyebrow">INSTITUTO DR ANDERSON GOIS</span>

                <h1>Cursos para evoluir na sua prática clínica</h1>

                <p>
                    Formação em ortodontia, DTM e bruxismo, estética e
                    harmonização, com teoria aplicada e prática supervisionada.
                </p>

                <form
                    action="{{ route('home') }}"
                    method="GET"
                    class="event-search-form"
                    role="search"
                >
                    <label for="search" class="visually-hidden">
                        Buscar cursos
                    </label>

                    <div class="search-input-wrapper">
                        <input
                            type="search"
                            id="search"
                            name="search"
                            class="form-control"
                            placeholder="Busque por curso, área ou cidade..."
                            value="{{ request('search') }}"
                        >

                        <button type="submit" class="btn btn-primary">
                            Buscar cursos
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Listagem de cursos -->
    <section class="events-section">
        <div class="container">

            <div class="events-section-header">
                <div>
                    <span class="section-eyebrow">AGENDA</span>
                    <h2>Próximos cursos</h2>
                    <p>Confira as turmas previstas e escolha sua próxima especialização.</p>
                </div>

                @if(auth()->user()?->isAdmin())
                    <a href="{{ route('events.create') }}"
                       class="btn btn-outline-primary create-event-button">
                        Novo curso
                    </a>
                @endif
            </div>

            @if($eventos->isNotEmpty())

                <div class="row g-4">

                    @foreach($eventos as $evento)

                        <div class="col-12 col-sm-6 col-lg-4 col-xl-3">

                            <article class="card event-card h-100">

                                <div class="event-image-wrapper">
                                    <img
                                        src="{{ $evento->imagemUrl() }}"
                                        alt="Capa do curso {{ $evento->titulo }}"
                                        class="event-image"
                                        loading="lazy"
                                    >
                                </div>

                                <div class="card-body event-card-body">

                                    @if($evento->area)
                                        <span class="event-card-area">{{ $evento->area }}</span>
                                    @endif

                                    <h3 class="card-title event-card-title">
                                        {{ $evento->titulo }}
                                    </h3>

                                    <p class="event-card-meta">
                                        {{ $evento->cidade }}
                                        @if($evento->data_inicio)
                                            · {{ $evento->data_inicio->format('d/m/Y') }}
                                        @endif
                                    </p>

                                    <p class="event-card-description">
                                        {{ Str::limit($evento->descricao, 110) }}
                                    </p>

                                    <div class="event-card-footer">
                                        <a
                                            href="{{ route('events.show', $evento) }}"
                                            class="btn btn-primary event-details-button"
                                        >
                                            Saiba mais
                                        </a>
                                    </div>

                                </div>

                            </article>

                        </div>

                    @endforeach

                </div>

            @else

                <div class="events-empty-state">
                    <h3>Nenhum curso encontrado</h3>

                    <p>
                        Não encontramos cursos para exibir.
                        Tente outra busca ou volte mais tarde.
                    </p>

                    <a href="{{ route('home') }}"
                       class="btn btn-outline-primary">
                        Ver todos os cursos
                    </a>
                </div>

            @endif

        </div>
    </section>

    <!-- Sobre o Dr Anderson Gois -->
    <section class="about-section">
        <div class="container">
            <div class="about-content">
                <img
                    src="{{ asset('img/dr-anderson-gois.jpg') }}"
                    alt="Retrato do Dr Anderson Gois"
                    class="about-photo"
                    loading="lazy"
                >

                <div>
                    <span class="section-eyebrow">QUEM CONDUZ</span>
                    <h2>Dr Anderson Gois</h2>
                    <p>
                        O Instituto nasceu para aproximar o conhecimento técnico
                        da rotina do consultório. Cada curso é pensado para que o
                        cirurgião-dentista saia preparado para diagnosticar,
                        planejar e atender com mais segurança.
                    </p>
                </div>
            </div>
        </div>
    </section>

</section>

@endsection
