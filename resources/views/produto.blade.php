@extends('layouts.main')

@section('titulo', 'Produto')

@section('content')

    <h1></h1>
    @if($id != null)
    <p>Exibindo produto id: {{$id}}</p>
    @endif

@endsection