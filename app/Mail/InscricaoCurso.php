<?php

namespace App\Mail;

use App\Models\Inscricao;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InscricaoCurso extends Mailable
{
    public function __construct(public Inscricao $inscricao) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->inscricao->email, $this->inscricao->nome)],
            subject: 'Nova inscrição: '.$this->inscricao->evento->titulo,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.inscricao');
    }
}
