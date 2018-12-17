<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Listado de Usuarios';
        return view('users.index', compact('users', 'title'));
    }
    public function show($id)
    {
        return view('users.show', compact('id'));
    }

    public function create()
    {
        return 'Crear nuevo usuario';
    }
}
