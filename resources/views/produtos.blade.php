@extends('layouts.main')

@section('titulo', 'produtos')

@section('content')

    <h1>Meus produtos</h1>

    @if($busca != '')
        <p>O usuarios esta buscando por: {{$busca}}</p>
    @endif

@endsection