
@extends('layouts.main')

@section('titulo', $evento->exists ? 'Editar curso' : 'Novo curso')

@section('content')

<section class="event-create-page">
    <div class="container">

        <div class="event-create-wrapper">

            <!-- Cabeçalho do formulário -->
            <div class="event-create-header">
                <span class="section-eyebrow">INSTITUTO DR ANDERSON GOIS</span>

                <h1>{{ $evento->exists ? 'Editar curso' : 'Novo curso' }}</h1>

                <p>
                    Preencha os dados da turma. Eles aparecem na agenda de
                    cursos do site.
                </p>
            </div>

            <!-- Formulário -->
            <div class="event-create-card">

                <form
                    action="{{ $evento->exists ? route('events.update', $evento) : route('events.store') }}"
                    method="POST"
                    class="event-create-form"
                    enctype="multipart/form-data"
                >
                    @csrf

                    @if($evento->exists)
                        @method('PUT')
                    @endif

                    <!-- Imagem -->
                    <div class="mb-4">
                        <label for="imagem" class="form-label">
                            Imagem do curso
                        </label>

                        <input
                            type="file"
                            class="form-control @error('imagem') is-invalid @enderror"
                            name="imagem"
                            id="imagem"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div class="form-text">
                            Opcional. JPG, PNG ou WEBP de até 4 MB.
                            @if($evento->exists)
                                Deixe em branco para manter a imagem atual.
                            @endif
                        </div>

                        @error('imagem')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Nome do curso -->
                    <div class="mb-4">
                        <label for="titulo" class="form-label">
                            Nome do curso
                        </label>

                        <input
                            type="text"
                            class="form-control @error('titulo') is-invalid @enderror"
                            id="titulo"
                            name="titulo"
                            value="{{ old('titulo', $evento->titulo) }}"
                            placeholder="Ex.: Ortodontia Clínica"
                            maxlength="255"
                            autocomplete="off"
                            required
                        >

                        @error('titulo')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Área -->
                    <div class="mb-4">
                        <label for="area" class="form-label">
                            Área
                        </label>

                        <select
                            name="area"
                            id="area"
                            class="form-select @error('area') is-invalid @enderror"
                            required
                        >
                            <option value="">Selecione</option>

                            @foreach(\App\Models\Evento::AREAS as $area)
                                <option value="{{ $area }}" @selected(old('area', $evento->area) === $area)>
                                    {{ $area }}
                                </option>
                            @endforeach
                        </select>

                        @error('area')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Cidade -->
                    <div class="mb-4">
                        <label for="cidade" class="form-label">
                            Cidade
                        </label>

                        <input
                            type="text"
                            class="form-control @error('cidade') is-invalid @enderror"
                            id="cidade"
                            name="cidade"
                            value="{{ old('cidade', $evento->cidade) }}"
                            placeholder="Em qual cidade acontecerá?"
                            maxlength="255"
                            autocomplete="address-level2"
                            required
                        >

                        @error('cidade')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Data de início -->
                        <div class="col-md-4 mb-4">
                            <label for="data_inicio" class="form-label">
                                Data de início
                            </label>

                            <input
                                type="date"
                                class="form-control @error('data_inicio') is-invalid @enderror"
                                id="data_inicio"
                                name="data_inicio"
                                value="{{ old('data_inicio', $evento->data_inicio?->format('Y-m-d')) }}"
                                required
                            >

                            @error('data_inicio')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Carga horária -->
                        <div class="col-md-4 mb-4">
                            <label for="carga_horaria" class="form-label">
                                Carga horária (horas)
                            </label>

                            <input
                                type="number"
                                class="form-control @error('carga_horaria') is-invalid @enderror"
                                id="carga_horaria"
                                name="carga_horaria"
                                value="{{ old('carga_horaria', $evento->carga_horaria) }}"
                                min="1"
                                max="5000"
                                required
                            >

                            @error('carga_horaria')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Valor -->
                        <div class="col-md-4 mb-4">
                            <label for="valor" class="form-label">
                                Valor (R$)
                            </label>

                            <input
                                type="number"
                                class="form-control @error('valor') is-invalid @enderror"
                                id="valor"
                                name="valor"
                                value="{{ old('valor', $evento->valor) }}"
                                min="0"
                                step="0.01"
                                required
                            >

                            @error('valor')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-4">
                        <label for="descricao" class="form-label">
                            Descrição do curso
                        </label>

                        <textarea
                            class="form-control @error('descricao') is-invalid @enderror"
                            id="descricao"
                            name="descricao"
                            rows="5"
                            placeholder="Conte sobre o conteúdo, a programação e o público do curso."
                            required
                        >{{ old('descricao', $evento->descricao) }}</textarea>

                        @error('descricao')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Privacidade -->
                    <div class="mb-4">
                        <label for="privado" class="form-label">
                            Visibilidade do curso
                        </label>

                        <select
                            name="privado"
                            id="privado"
                            class="form-select @error('privado') is-invalid @enderror"
                            required
                        >
                            <option
                                value="0"
                                @selected((string) old('privado', (int) $evento->privado) === '0')
                            >
                                Público: aparece na agenda do site
                            </option>

                            <option
                                value="1"
                                @selected((string) old('privado', (int) $evento->privado) === '1')
                            >
                                Privado: visível só para a equipe
                            </option>
                        </select>

                        @error('privado')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Erros gerais -->
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            Verifique os campos destacados e tente novamente.
                        </div>
                    @endif

                    <!-- Ações -->
                    <div class="event-create-actions">
                        <a
                            href="{{ $evento->exists ? route('events.show', $evento) : route('home') }}"
                            class="btn btn-light cancel-button"
                        >
                            Cancelar
                        </a>

                        <button type="submit" class="btn btn-primary submit-button">
                            {{ $evento->exists ? 'Salvar alterações' : 'Criar curso' }}
                        </button>
                    </div>

                </form>

            </div>

        </div>

    </div>
</section>

@endsection
