<?php

namespace App\Mail;

use App\Models\Evento;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InscricaoCurso extends Mailable
{
    public function __construct(
        public Evento $evento,
        public array $dados,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->dados['email'], $this->dados['nome'])],
            subject: 'Nova inscrição: '.$this->evento->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inscricao');
    }
}
