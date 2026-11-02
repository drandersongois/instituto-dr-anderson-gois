<!DOCTYPE html>
<html lang="pt-BR">
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h2 style="margin: 0 0 16px;">Nova inscrição em curso</h2>

    <p><strong>Curso:</strong> {{ $evento->titulo }}</p>
    <p><strong>Nome:</strong> {{ $dados['nome'] }}</p>
    <p><strong>E-mail:</strong> {{ $dados['email'] }}</p>
    <p><strong>Telefone:</strong> {{ $dados['telefone'] }}</p>

    @if(! empty($dados['cro']))
        <p><strong>CRO:</strong> {{ $dados['cro'] }}</p>
    @endif

    @if(! empty($dados['mensagem']))
        <p><strong>Mensagem:</strong></p>
        <p style="white-space: pre-line;">{{ $dados['mensagem'] }}</p>
    @endif
</body>
</html>
