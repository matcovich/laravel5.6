<?php
Route::get('/', function () {
    return view('welcome');
});
Route::get('/usuarios', 'UserController@index')
    ->name('users.index');

Route::get('/usuarios/{user}', 'UserController@show')
    ->where('user', '[0-9]+')
    ->name('users.show');

Route::get('/usuarios/nuevo', 'UserController@create')->name('users.create');

Route::post('/usuarios', 'UserController@store');

Route::get('/usuarios/{user}/editar', 'UserController@edit')->name('users.edit');

Route::put('/usuarios/{user}', 'UserController@update');

Route::delete('/usuarios/{user}', 'UserController@destroy')->name('users.destroy');


Route::get('/saludo/{name}/{nickname?}','WelcomeUserController@index');

// Profile
Route::get('/editar-perfil/', 'ProfileController@edit');
Route::put('/editar-perfil/', 'ProfileController@update');
// Professions
Route::get('/profesiones/', 'ProfessionController@index');
Route::delete('/profesiones/{profession}', 'ProfessionController@destroy');
// Skills
Route::get('/habilidades/', 'SkillController@index');