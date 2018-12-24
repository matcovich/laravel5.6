<?php

namespace Tests\Feature\Admin;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListUsersTest extends TestCase
{
    use RefreshDatabase;
    /** @test */
    function it_loads_the_users_list_page()
    {
        factory(User::class)->create([
            'name' => 'Ellie',
        ]);
        factory(User::class)->create([
            'name' => 'Joel',
        ]);
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('de Usuarios')
            ->assertSee('Joel')
            ->assertSee('Ellie');
    }
    /** @test */
    function it_shows_a_default_message_if_the_users_list_is_empty()
    {
        $this->get('/usuarios')
            ->assertStatus(200)
            ->assertSee('No hay usuarios registrados.');
    }
}