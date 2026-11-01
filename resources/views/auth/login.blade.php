@extends('layouts.main')

@section('titulo', 'Entrar')

@section('content')

<section class="event-create-page">
    <div class="container">
        <div class="event-create-wrapper login-wrapper">

            <div class="event-create-header">
                <span class="section-eyebrow">ÁREA DA EQUIPE</span>
                <h1>Entrar</h1>
                <p>Acesse para gerenciar alunos, cursos e agendamentos.</p>
            </div>

            <div class="event-create-card">
                <form action="{{ route('login') }}" method="POST" class="event-create-form">
                    @csrf

                    <div class="mb-4">
                        <label for="usuario" class="form-label">Usuário</label>

                        <input
                            type="text"
                            class="form-control @error('usuario') is-invalid @enderror"
                            id="usuario"
                            name="usuario"
                            value="{{ old('usuario') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >

                        @error('usuario')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Senha</label>

                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                        >

                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-100 login-button">
                        Entrar
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>

@endsection
