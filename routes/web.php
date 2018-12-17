<?php
Route::get('/', function () {
    return 'Home';
});
Route::get('/usuarios', 'UserController@index')->name('users');
Route::get('/usuarios/{user}', 'UserController@show')
    ->where('user', '[0-9]+')
    ->name('users.show');
Route::get('/usuarios/nuevo', 'UserController@create');
Route::get('/saludo/{name}/{nickname?}','WelcomeUserController@index');
