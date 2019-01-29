<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => [
                'required', 'email',
                Rule::unique('users')->ignore($this->user)
            ],
            'password' => '',
            'role' => [Rule::in(Role::getList())],
            'bio' => 'required',
            'twitter' => ['nullable', 'present', 'url'],
            'profession_id' => [
                'nullable', 'present',
                Rule::exists('professions', 'id')->whereNull('deleted_at')
            ],
            'skills' => [
                'array',
                Rule::exists('skills', 'id'),
            ],
        ];
    }

    public function updateUser(User $user)
    {
        $user->fill([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'role' => $this->role,
        ]);
        if ($this->password != null) {
            $user->password = bcrypt($this->password);
        }
        $user->save();

        $user->profile->update([
            'twitter' => $this->twitter,
            'bio' => $this->bio,
            'profession_id' => $this->profession_id,
        ]);
        $user->skills()->sync($this->skills ?: []);
    }

    public function messages()
    {
        return [
            'first_name.required' => 'El campo nombre es obligatorio',
            'last_name.required' => 'El campo apellido es obligatorio',
            'email.required' => 'Ingresa una direccion de Correo Electrónico Válida',
            'email.unique' => 'Ya existe un Usuario Con esta direccion de correo Electrónico',
            'password.required' => 'El Campo password es Requerido',
            'password.min' => 'El password debe ser mayor de 6 caracteres'
        ];
    }
}

