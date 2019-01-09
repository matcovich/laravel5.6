<?php

namespace App\Http\Controllers;

use App\Http\Forms\UserForm;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;
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
        return new UserForm('users.create', new User);
    }
    public function store(CreateUserRequest $request)
    {
        $request->createUser();

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return new UserForm('users.edit', $user);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $request->updateUser($user);
        return redirect()->route('users.show', ['user' => $user]);
    }


    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }

}
