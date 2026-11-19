<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('authentication pages and the signed-in profile render', function () {
    foreach (['/login', '/register', '/forgot-password', '/reset-password/example-token?email=user%40example.test'] as $path) {
        $this->get($path)->assertOk();
    }

    $user = User::factory()->create(['name' => 'Vendedor Uno']);
    $this->actingAs($user)->get('/profile')->assertOk()->assertSee('Vendedor Uno');
});

test('validation errors remain visible after a regular form submission', function () {
    $this->from('/register')->followingRedirects()
        ->post('/register', ['name' => 'Vendedor', 'email' => 'invalid'])
        ->assertOk()->assertSee('aria-invalid="true"', false);
});

test('guests cannot access crm pages or endpoints', function () {
    foreach (['/', '/dashboard', '/companies', '/contacts', '/deals', '/activities', '/profile'] as $path) {
        $this->get($path)->assertRedirect(route('login'));
    }

    $this->postJson('/companies', [])->assertUnauthorized();
});

test('registration creates a seller and ignores submitted roles', function () {
    $response = $this->post('/register', [
        'name' => 'Vendedor Uno',
        'email' => 'seller@example.test',
        'password' => 'example-password',
        'password_confirmation' => 'example-password',
        'role' => 'admin',
    ]);

    $user = User::where('email', 'seller@example.test')->firstOrFail();
    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
    expect($user->role)->toBe('vendedor')
        ->and(Hash::check('example-password', $user->password))->toBeTrue();
});

test('login rejects invalid credentials and logout ends the session', function () {
    $user = User::factory()->create(['password' => 'example-password']);

    $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->post('/login', ['email' => $user->email, 'password' => 'example-password'])
        ->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    $this->post('/logout')->assertRedirect(route('login'));
    $this->assertGuest();
    $this->get('/dashboard')->assertRedirect(route('login'));
});

test('password reset notifications use app url and tokens can be used once', function () {
    Notification::fake();
    config(['app.url' => 'https://crm.example.test']);
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');
    Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
        $url = $notification->toMail($user)->actionUrl;

        return str_starts_with($url, 'https://crm.example.test/reset-password/');
    });

    $token = Password::broker()->createToken($user);
    $payload = [
        'email' => $user->email,
        'token' => $token,
        'password' => 'new-example-password',
        'password_confirmation' => 'new-example-password',
    ];
    $this->post('/reset-password', $payload)->assertRedirect(route('login'));
    expect(Hash::check('new-example-password', $user->fresh()->password))->toBeTrue();
    $this->post('/reset-password', $payload)->assertSessionHasErrors('email');
});

test('profile updates cannot alter the role and password changes require the current password', function () {
    $user = User::factory()->create(['password' => 'example-password']);
    $this->actingAs($user);

    $this->patch('/profile', ['name' => 'Nuevo Nombre', 'email' => 'new@example.test', 'role' => 'admin'])
        ->assertRedirect(route('profile.edit'));
    expect($user->fresh()->name)->toBe('Nuevo Nombre')
        ->and($user->fresh()->email)->toBe('new@example.test')
        ->and($user->fresh()->role)->toBe('vendedor');

    $this->put('/profile/password', [
        'current_password' => 'wrong-password',
        'password' => 'new-example-password',
        'password_confirmation' => 'new-example-password',
    ])->assertSessionHasErrors('current_password');
    expect(Hash::check('example-password', $user->fresh()->password))->toBeTrue();

    $this->put('/profile/password', [
        'current_password' => 'example-password',
        'password' => 'new-example-password',
        'password_confirmation' => 'new-example-password',
    ])->assertRedirect(route('profile.edit'));
    expect(Hash::check('new-example-password', $user->fresh()->password))->toBeTrue();
});

test('administrators are promoted explicitly from an existing account', function () {
    $user = User::factory()->create();

    $this->artisan('crm:make-admin', ['email' => $user->email])->assertSuccessful();
    expect($user->fresh()->role)->toBe('admin');
    $this->artisan('crm:make-admin', ['email' => 'missing@example.test'])->assertFailed();
});
