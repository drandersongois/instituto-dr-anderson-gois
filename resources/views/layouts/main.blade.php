
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="theme-color" content="#0a0a0a">
    <meta name="description" content="Cursos de ortodontia, DTM e bruxismo, estética e harmonização no Instituto Dr Anderson Gois.">

    <title>@yield('titulo', 'Cursos') | Instituto Dr Anderson Gois</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Source+Serif+4:wght@600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Bootstrap 5.3.8 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >

    <!-- CSS personalizado -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- JavaScript próprio -->
    <script src="{{ asset('js/script.js') }}" defer></script>
</head>

<body>

    <header class="site-header">
        <nav class="navbar navbar-expand-lg" data-bs-theme="dark" aria-label="Navegação principal">
            <div class="container">

                <!-- Logo -->
                <a class="navbar-brand" href="{{ route('home') }}" aria-label="Instituto Dr Anderson Gois - Página inicial">
                    <img
                        src="{{ asset('img/logo_instituto.svg') }}"
                        alt="Instituto Dr Anderson Gois"
                        class="site-logo"
                    >
                </a>

                <!-- Botão do menu mobile -->
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarPrincipal"
                    aria-controls="navbarPrincipal"
                    aria-expanded="false"
                    aria-label="Abrir ou fechar menu"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Links de navegação -->
                <div class="collapse navbar-collapse" id="navbarPrincipal">
                    <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                Cursos
                            </a>
                        </li>

                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('inscricoes.index') }}">
                                    Inscrições
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('clientes.index') }}">
                                    Alunos
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('agendamentos.index') }}">
                                    Agendamentos
                                </a>
                            </li>

                            @if(auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('events.create') }}">
                                        Novo curso
                                    </a>
                                </li>
                            @endif

                            <li class="nav-item">
                                <span class="nav-user">
                                    {{ auth()->user()->name }}
                                    <small>{{ auth()->user()->isAdmin() ? 'Administrador' : 'Atendente' }}</small>
                                </span>
                            </li>

                            <li class="nav-item">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary nav-logout">
                                        Sair
                                    </button>
                                </form>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="btn btn-primary nav-register" href="{{ route('login') }}">
                                    Entrar
                                </a>
                            </li>
                        @endauth

                    </ul>
                </div>

            </div>
        </nav>
    </header>


    <!-- Conteúdo específico de cada página -->
    <main class="site-main">

        @if (session('msg'))
            <div class="alert alert-success alert-dismissible fade show app-alert" role="alert">
                {{ session('msg') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fechar mensagem"
                ></button>
            </div>
        @endif

        @if (session('erro'))
            <div class="alert alert-danger alert-dismissible fade show app-alert" role="alert">
                {{ session('erro') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Fechar mensagem"
                ></button>
            </div>
        @endif

        @yield('content')

    </main>

    <!-- Rodapé -->
    <footer class="site-footer">
        <div class="container">
            <p class="site-footer-contact">
                @if(config('instituto.telefone'))
                    <a href="tel:+55{{ preg_replace('/\D/', '', config('instituto.telefone')) }}">{{ config('instituto.telefone') }}</a>
                @endif

                <a
                    href="https://www.instagram.com/{{ config('instituto.instagram') }}/"
                    target="_blank"
                    rel="noopener noreferrer"
                >{{ '@'.config('instituto.instagram') }}</a>
            </p>

            <p class="mb-0">
                &copy; {{ date('Y') }} Instituto Dr Anderson Gois.
                Todos os direitos reservados.
            </p>
        </div>
    </footer>

    <!-- JavaScript do Bootstrap -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"
    ></script>

</body>
</html>
