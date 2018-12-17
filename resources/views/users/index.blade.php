@extends('layout')

@section('content')
            <h1>{{ $title }}</h1>
            <hr>
            <ul>
                @forelse($users as $user)
                    <li>{{ $user->name }} -
                        <small class="text-muted">{{$user->email}}</small> |
                        <a href="{{route('users.show',['id'=> $user->id])}}" class="text-success" >ver detalle</a>
                    </li>

                @empty
                    <li>No hay usuarios registrados.</li>
                @endforelse
            </ul>

@endsection

