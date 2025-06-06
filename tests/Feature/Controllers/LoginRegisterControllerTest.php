<?php

namespace Tests\Feature\Auth;

use App\Models\users\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
uses();
describe('Login', function () {
    it('shows the login form', function () {
        $response = $this->get('/login');
        $response->assertStatus(200)
            ->assertViewIs('auth.login');
    });
    it('authenticates users with correct credentials and redirects to welcome for regular users', function () {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('welcome'))
            ->assertSessionHas('success', 'Welcome back!');
        $user->delete();
    });
    it('authenticates users with correct credentials and redirects to admin dashboard for admin users', function () {
        if (!Role::where('name', 'admin')->exists()) {
            Role::create(['name' => 'admin']);
        }
        $admin = User::factory()->create(['password' => Hash::make('password')])->assignRole('admin');
        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success', 'Welcome admin!');
        $admin->delete();
    });
    it('does not authenticate users with incorrect credentials', function () {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
        $user->delete();
    });
    it('logs out authenticated users and redirects to home', function () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/logout');
        $this->assertGuest();
        $response->assertRedirect('/');
        $user->delete();
    });
});
describe('Register', function () {
    it('shows the registration form', function () {
        $response = $this->get('/register');
        $response->assertStatus(200)->assertViewIs('auth.register');
    });
    /**
    it('registers a new user and redirects to email verification', function () {
        Event::fake();
        $userData = [
            'name' => 'Test User',
            'email' => 'test' . now()->timestamp . '@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
        $response = $this->post('/register', $userData);
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
        $user = Auth::user();
        $this->assertTrue($user->hasRole('user'));
        Event::assertDispatched(Registered::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
        $response->assertRedirect(route('verification.notice'));
        $user->delete();
    });
     * **/
    it('does not register a user with invalid data', function () {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'invalid-email']);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    });
});
