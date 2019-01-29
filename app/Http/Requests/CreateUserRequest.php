<?php

namespace App\Http\Requests;

use App\Role;
use App\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required','email', 'unique:users,email'],
            'password' => 'required|min:6',
            'role' => ['nullable', Rule::in(Role::getList())],
            'bio' => 'required',
            'twitter' => ['nullable','url'],
            'profession_id' => [
                'nullable', 'present',
                Rule::exists('professions', 'id')->whereNull('deleted_at')
                ],
            'skills'=> [
                'array',
                Rule::exists('skills', 'id'),
            ],
        ];
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

    public function createUser()
    {
        DB::transaction(function (){
            $data = $this->validated();

            $user = new User([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            $user->role = $data['role'] ?? 'user';

            $user->save();

            $user->profile()->create([
                'bio' => $data['bio'],
                'twitter' => $data['twitter'],
                'profession_id' => $data['profession_id'],

            ]);

            if (! empty($data['skills'])){
                $user->skills()->attach($data['skills']);
            }
        });
    }
}
