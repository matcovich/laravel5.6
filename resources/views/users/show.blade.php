@extends('layout')
@section('title', "Usuario {$user->id}")
@section('content')
    <h1>Usuario #{{ $user->id }}</h1>

    <p>Nombre del usuario: <b>{{$user->name}}</b> </p>
    <p>Correo de <b>{{$user->name}}</b>: {{$user->email}}</p>
    <p>
        <a href="{{ route('users.index')}}"> regresar</a>
    </p>

@endsection

