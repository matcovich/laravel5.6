<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\User;
use App\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        $title = 'Listado de Usuarios';
        return view('users.index', compact('users', 'title'));
    }
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        return view('users.create');
    }
    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(User $user)
    {
        $data = request()->validate([
            'name' => 'required',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => '',
        ], [
            'name.required' => 'El campo nombre es obligatorio',
            'email.required' => 'Ingresa una direccion de Correo Electrónico Válida',
            'email.unique' => 'Ya existe un Usuario Con esta direccion de correo Electrónico',
            'password.required' => 'El Campo password es Requerido',
            'password.min' => 'El password debe ser mayor de 6 caracteres'
        ]);

        if ($data['password'] !=null){
            $data['password'] = bcrypt($data['password']);
        }else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.show', ['user'=>$user]);
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

}
