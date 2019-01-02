@extends('layout')
@section('title', "Crear usuario")
@section('content')
    @card
        @slot('header', 'Crear nuevo usuario')
            {{ new App\Http\ViewComponents\UserFields($user) }}

            <form method="POST" action="{{ url('usuarios') }}">
                @render('UserFields', compact('user'))
                <button type="submit" class="btn btn-primary">Crear usuario</button>
                <a href="{{ route('users.index') }}" class="btn btn-link">Regresar al listado de usuarios</a>
            </form>
    @endcard
@endsection