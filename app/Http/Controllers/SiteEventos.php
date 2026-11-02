<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiteEventos extends Controller
{
    public function index(Request $request)
    {
        $busca = trim((string) $request->query('search'));

        $eventos = Evento::query()
            ->when(! $request->user(), fn ($query) => $query->where('privado', false))
            ->when($busca !== '', function ($query) use ($busca) {
                $query->where(function ($query) use ($busca) {
                    $query->where('titulo', 'like', "%{$busca}%")
                        ->orWhere('area', 'like', "%{$busca}%")
                        ->orWhere('cidade', 'like', "%{$busca}%");
                });
            })
            ->orderBy('data_inicio')
            ->get();

        return view('welcome', ['eventos' => $eventos]);
    }

    public function show(Request $request, Evento $evento)
    {
        abort_if($evento->privado && ! $request->user(), 404);

        return view('events.show', ['evento' => $evento]);
    }

    public function create()
    {
        return view('events.create', ['evento' => new Evento]);
    }

    public function store(Request $request)
    {
        $evento = new Evento($this->validar($request));
        $this->salvarImagem($request, $evento);
        $evento->save();

        return redirect()->route('home')->with('msg', 'Curso criado com sucesso!');
    }

    public function edit(Evento $evento)
    {
        return view('events.create', ['evento' => $evento]);
    }

    public function update(Request $request, Evento $evento)
    {
        $evento->fill($this->validar($request));
        $this->salvarImagem($request, $evento);
        $evento->save();

        return redirect()->route('events.show', $evento)->with('msg', 'Curso atualizado com sucesso!');
    }

    public function destroy(Evento $evento)
    {
        if ($evento->agendamentos()->exists()) {
            return back()->with('erro', 'Este curso tem agendamentos e não pode ser excluído.');
        }

        if ($evento->inscricoes()->exists()) {
            return back()->with('erro', 'Este curso tem inscrições e não pode ser excluído.');
        }

        $this->apagarImagem($evento);
        $evento->delete();

        return redirect()->route('home')->with('msg', 'Curso excluído com sucesso!');
    }

    private function validar(Request $request): array
    {
        return $request->validate([
            'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'titulo' => ['required', 'string', 'max:255'],
            'area' => ['required', Rule::in(Evento::AREAS)],
            'cidade' => ['required', 'string', 'max:255'],
            'data_inicio' => ['required', 'date'],
            'carga_horaria' => ['required', 'integer', 'min:1', 'max:5000'],
            'valor' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'descricao' => ['required', 'string'],
            'privado' => ['required', 'boolean'],
        ]);
    }

    private function salvarImagem(Request $request, Evento $evento): void
    {
        if (! $request->hasFile('imagem')) {
            return;
        }

        $this->apagarImagem($evento);

        $imagem = $request->file('imagem');
        $nome = md5($imagem->getClientOriginalName().microtime()).'.'.$imagem->extension();
        $imagem->move(public_path('img/eventos_img'), $nome);

        $evento->imagem = $nome;
    }

    /**
     * Apaga só imagens enviadas por upload (nome em md5), nunca as capas do projeto.
     */
    private function apagarImagem(Evento $evento): void
    {
        if ($evento->imagem && preg_match('/^[a-f0-9]{32}\.\w+$/', $evento->imagem)) {
            $caminho = public_path('img/eventos_img/'.$evento->imagem);

            if (is_file($caminho)) {
                unlink($caminho);
            }
        }
    }
}
