@extends('layout')

@section('title', "Página no encontrada")

@section('content')
    <h1>Página no encontrada</h1>

    <p><a href="{{route('users.index')}}">Volver a usuarios</a></p>
@endsection