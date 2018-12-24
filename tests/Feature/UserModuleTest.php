<?php

namespace Tests\Feature;

use App\Profession;
use App\Skill;
use App\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModuleTest extends TestCase
{
    use RefreshDatabase;
    protected $profession;
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
        ->assertSee('Usuarios')
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
    /** @test */
    function it_displays_the_users_details()
    {
        $this->withExceptionHandling();
        $user = factory(User::class)->create([
            'name' => 'Duilio Palacios'
        ]);

        $this->get('/usuarios/'.$user->id)
            ->assertStatus(200)
            ->assertSee('Duilio Palacios');
    }
    /** @test */
    function it_displays_a_404_error_if_the_user_is_not_found()
    {


        $this->get('/usuarios/999')
            ->assertStatus(404)
            ->assertSee('Página no encontrada');
    }
    /** @test */
    function it_loads_the_new_users_page()
    {
        $this->withoutExceptionHandling();

        $profession = factory(Profession::class)->create();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();

        $this->get('/usuarios/nuevo')
            ->assertStatus(200)
            ->assertSee('Crear usuario')
            ->assertViewHas('professions', function($professions)use ($profession){
                return $professions->contains($profession);
            })
        ->assertViewHas('skills', function ($skills) use ($skillA, $skillB){
            return $skills->contains($skillA)  && $skills->contains($skillB);
        });
    }
    /** @test */
    function it_creates_a_new_user()
    {
        $this->withoutExceptionHandling();

        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $skillC = factory(Skill::class)->create();

        $this->post('/usuarios/', $this->getValidData([
            'skills' => [$skillA->id, $skillB->id],
        ]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456',
        ]);

        $user = User::findByEmail('duilio@styde.net');

        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/sileence',
            'user_id' => User::findByEmail('duilio@styde.net')->id,
            'profession_id' => $this->profession->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillA->id,
        ]);

        $this->assertDatabaseHas('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillB->id,
        ]);
        $this->assertDatabaseMissing('user_skill', [
            'user_id' => $user->id,
            'skill_id' => $skillC->id,
        ]);
    }


    /** @test */
    function the_twitter_field_is_optional()
    {
        $this->withoutExceptionHandling();
        $this->post('/usuarios/', $this->getValidData([
            'twitter' => null,
        ]))->assertRedirect('usuarios');

        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456',
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => null,
            'user_id' => User::findByEmail('duilio@styde.net')->id,
        ]);
    }

    /** @test */
    function the_profession_id_field_is_optional()
    {
        $this->withoutExceptionHandling();
        $this->post('/usuarios/', $this->getValidData([
            'profession_id' => null,
        ]))->assertRedirect('usuarios');
        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456',
        ]);
        $this->assertDatabaseHas('user_profiles', [
            'bio' => 'Programador de Laravel y Vue.js',
            'user_id' => User::findByEmail('duilio@styde.net')->id,
            'profession_id' => null,
        ]);
    }

    /** @test */
    function the_name_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'name' => '',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['name' => 'El campo nombre es obligatorio']);
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_email_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'email' => '',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0, User::count());
    }
    /** @test */
    function the_email_must_be_valid()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'email' => 'correo-no-valido',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(0, User::count());
    }
    /** @test */
    function the_email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'duilio@styde.net'
        ]);
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'email'=> 'duilio@styde.net',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['email']);
        $this->assertEquals(1, User::count());
    }
    /** @test */
    function the_password_is_required()
    {
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'password' => '',
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['password']);
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_profession_must_be_valid()
    {
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'profession_id' => '999'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function only_not_deleted_professions_can_be_selected()
    {
        $deletedProfession = factory(Profession::class)->create([
            'deleted_at' => now()->format('Y-m-d'),
        ]);
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'profession_id' => $deletedProfession->id,
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['profession_id']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function the_skills_must_be_an_array()
    {
        $this->handleValidationExceptions();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'skills' => 'PHP, JS'
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']);
        $this->assertDatabaseEmpty('users');
    }
    /** @test */
    function the_skills_must_be_valid()
    {
        $this->handleValidationExceptions();
        $skillA = factory(Skill::class)->create();
        $skillB = factory(Skill::class)->create();
        $this->from('usuarios/nuevo')
            ->post('/usuarios/', $this->getValidData([
                'skills' => [$skillA->id, $skillB->id + 1],
            ]))
            ->assertRedirect('usuarios/nuevo')
            ->assertSessionHasErrors(['skills']);
        $this->assertDatabaseEmpty('users');
    }

    /** @test */
    function it_loads_the_edit_users_page()
    {
        $user = factory(User::class)->create();

        $this->get("usuarios/{$user->id}/editar")
            ->assertStatus(200)
            ->assertViewIs('users.edit')
            ->assertSee('Editar usuario')
            ->assertViewHas('user', function ($viewUser) use ($user) {
                return $viewUser->id === $user->id;
            });
    }


    /** @test */
    function it_updates_a_user()
    {
        $user = factory(User::class)->create();

        $this->withoutExceptionHandling();
        $this->put("/usuarios/{$user->id}", [
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456'
        ])->assertRedirect("/usuarios/{$user->id}");

        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456',
        ]);
    }
    /** @test */
    function the_name_is_required_when_updating_the_user()
    {
        $user = factory(User::class)->create();

        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => '',
                'email' => 'duilio@styde.net',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")

            ->assertSessionHasErrors(['name']);

        $this->assertDatabaseMissing('users', ['email' => 'duilio@styde.net']);
    }
    /** @test */
    function the_email_must_be_valid_when_updating_the_user()
    {
        $user = factory(User::class)->create();
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio Palacios',
                'email' => 'correo-no-valido',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('users', ['name' => 'Duilio Palacios']);
    }
    /** @test */
    function the_email_must_be_unique_when_updating_the_user()
    {
        //$this->withoutExceptionHandling();
        factory(User::class)->create([
            'email' => 'existing-email@example.com',
        ]);
        $user = factory(User::class)->create([
            'email' => 'duilio@styde.net'
        ]);
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio',
                'email' => 'existing-email@example.com',
                'password' => '123456'
            ])
            ->assertRedirect("usuarios/{$user->id}/editar")
            ->assertSessionHasErrors(['email']);
        //
    }
    /** @test */
    function the_users_email_can_stay_the_same_when_updating_the_user()
    {
        $user = factory(User::class)->create([
            'email' => 'duilio@styde.net'
        ]);
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio Palacios',
                'email' => 'duilio@styde.net',
                'password' => '12345678'
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)
        $this->assertDatabaseHas('users', [
            'name' => 'Duilio Palacios',
            'email' => 'duilio@styde.net',
        ]);
    }
    /** @test */
    function the_password_is_optional_when_updating_the_user()
    {
        $oldPassword = 'CLAVE_ANTERIOR';
        $user = factory(User::class)->create([
            'password' => bcrypt($oldPassword)
        ]);
        $this->from("usuarios/{$user->id}/editar")
            ->put("usuarios/{$user->id}", [
                'name' => 'Duilio',
                'email' => 'duilio@styde.net',
                'password' => ''
            ])
            ->assertRedirect("usuarios/{$user->id}"); // (users.show)
        $this->assertCredentials([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => $oldPassword // VERY IMPORTANT!
        ]);
    }

    /** @test */
    function it_deletes_a_user()
    {
        $this->withoutExceptionHandling();
        $user = factory(User::class)->create();
        $this->delete("usuarios/{$user->id}")
            ->assertRedirect('usuarios');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
        //$this->assertSame(0, User::count());
    }

    protected function getValidData(array $custom = [])
    {
        $this->profession = factory(Profession::class)->create();
        return array_merge([
            'name' => 'Duilio',
            'email' => 'duilio@styde.net',
            'password' => '123456',
            'profession_id' => $this->profession->id,
            'bio' => 'Programador de Laravel y Vue.js',
            'twitter' => 'https://twitter.com/sileence',
        ], $custom);
    }

}
