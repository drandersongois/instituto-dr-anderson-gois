<!DOCTYPE html>
<html lang="pt-BR">
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="margin: 0 0 16px;">Nova inscrição em curso</h2>

    <p><strong>Data da inscrição:</strong> {{ $inscricao->dataInscricao()->format('d/m/Y \à\s H:i') }}</p>
    <p><strong>Curso:</strong> {{ $inscricao->evento->titulo }}</p>
    <p><strong>Nome:</strong> {{ $inscricao->nome }}</p>
    <p><strong>E-mail:</strong> {{ $inscricao->email }}</p>
    <p><strong>Telefone:</strong> {{ $inscricao->telefone }}</p>
    <p><strong>CPF:</strong> {{ $inscricao->cpfFormatado() }}</p>
    <p><strong>Data de nascimento:</strong> {{ $inscricao->data_nascimento->format('d/m/Y') }}</p>
    <p><strong>Sexo:</strong> {{ $inscricao->sexo }}</p>
    <p><strong>Endereço:</strong> {{ $inscricao->endereco }}</p>

    @if($inscricao->cro)
        <p><strong>CRO:</strong> {{ $inscricao->cro }}</p>
    @endif

    @if($inscricao->mensagem)
        <p><strong>Mensagem:</strong></p>
        <p style="white-space: pre-line;">{{ $inscricao->mensagem }}</p>
    @endif
</body>
</html>
