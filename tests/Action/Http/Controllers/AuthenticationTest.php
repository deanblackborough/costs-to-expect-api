<?php

namespace Tests\Action\Http\Controllers;

use App\User;
use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    public function testCheckSuccess(): void
    {
        $this->actingAs(User::find(1));

        $this->get('v3/auth/check')->assertExactJson(['auth'=>true]);
    }

    public function testCheckFalse(): void
    {
        $this->get('v3/auth/check')->assertExactJson(['auth'=>false]);
    }

    public function testCreateNewPasswordErrorsWithInvalidEmail(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-new-password.parameters.encrypted_token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $this->faker->email, 'encrypted_token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    public function testCreateNewPasswordErrorsWithInvalidToken(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'encrypted_token' => $this->faker->uuid]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    public function testCreateNewPasswordErrorsWithInvalidTokenAndEmail(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.create-new-password', ['email' => $this->faker->colorName, 'encrypted_token' => $this->faker->colorName]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(404);
    }

    public function testCreateNewPasswordErrorsWithNoPayload(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-new-password.parameters.encrypted_token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'encrypted_token' => $token]),
            []
        );

        $response->assertStatus(422);
    }

    public function testCreateNewPasswordSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-new-password.parameters.encrypted_token');

        $response = $this->post(
            route('auth.create-new-password', ['email' => $email, 'encrypted_token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);
    }

    public function testCreatePasswordErrorsWithInvalidEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $token]),
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(401);
    }

    public function tesCreatePasswordErrorsWithInvalidToken(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $this->faker->uuid]),
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(401);
    }

    public function testCreatePasswordErrorsWithInvalidTokenAndEmail(): void
    {
        $response = $this->post(
            route('auth.create-password', ['email' => $this->faker->email, 'token' => $this->faker->uuid]),
            []
        );

        $response->assertStatus(401);
    }

    public function testCreatePasswordFailsWithNoPayload(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
            ]
        );

        $response->assertStatus(422);
    }

    public function testCreatePasswordFailsWithInvalidPayload(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(422);
    }

    public function testCreatePasswordSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);
    }

    public function testForgotPasswordErrorsWithBadEmail(): void
    {
        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    public function testForgotPasswordErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.forgot-password'),
            []
        );

        $response->assertStatus(422);
    }

    public function testForgotPasswordSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.forgot-password'),
            [
                'email' => $email
            ]
        );

        $response->assertStatus(201);
    }

    public function testLoginErrorsWithBadEmail(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.login'),
            [
                'email' => $this->faker->email,
                'password' => $password,
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginErrorsWithBadPassword(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.login'),
            [
                'email' => $email,
                'password' => $this->faker->password(12),
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginSuccess(): void
    {
        $email = $this->faker->email;
        $password = $this->faker->password(12);

        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $email
            ]
        );

        $response->assertStatus(201);

        $token = $response->json('uris.create-password.parameters.token');

        $response = $this->post(
            route('auth.create-password', ['email' => $email, 'token' => $token]),
            [
                'password' => $password,
                'password_confirmation' => $password
            ]
        );

        $response->assertStatus(204);

        $response = $this->post(
            route('auth.login'),
            [
                'email' => $email,
                'password' => $password
            ]
        );

        $response->assertStatus(201);
    }

    public function testLoginErrorsWithNoEmail(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
                'name' => $this->faker->name,
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginErrorsWithNoName(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
                'email' => $this->faker->email,
            ]
        );

        $response->assertStatus(422);
    }

    public function testLoginErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.login'),
            [
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithBadEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNoEmail(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNoName(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNonUniqueEmail(): void
    {
        $email = $this->faker->email;

        $response = $this->post(
            route('auth.register'),
            [
                'email' => $email,
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(201);

        $response = $this->post(
            route('auth.register'),
            [
                'email' => $email,
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(422);
    }

    public function testRegistrationErrorsWithNoPayload(): void
    {
        $response = $this->post(
            route('auth.register'),
            []
        );

        $response->assertStatus(422);
    }

    public function testRegistrationSuccess(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $this->faker->email
            ]
        );

        $response->assertStatus(201);
    }

    public function testRegistrationSuccessSetRegisteredVia(): void
    {
        $response = $this->post(
            route('auth.register'),
            [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'registered_via' => 'budget-pro'
            ]
        );

        $response->assertStatus(201);
    }

    public function testUpdatePasswordFailsMismatchedPasswords(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v3/auth/update-password',
            [
                'password' => $this->faker->password(12),
                'password_confirmation' => $this->faker->password(12)
            ]
        );

        $response->assertStatus(422);
    }

    public function testUpdatePasswordFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v3/auth/update-password',
            [
            ]
        );

        $response->assertStatus(422);
    }

    public function testUpdatePasswordSuccess(): void
    {
        $this->createUser();
        
        $this->actingAs(User::find($this->fetchRandomUser()->id));

        $new_password = $this->faker->password(12);

        $response = $this->post(
            'v3/auth/update-password',
            [
                'password' => $new_password,
                'password_confirmation' => $new_password
            ]
        );

        $response->assertStatus(204);
    }

    public function testUpdateProfileFailsBadEmail(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v3/auth/update-profile',
            [
                'email' => 'email.email.com'
            ]
        );

        $response->assertStatus(422);
    }

    public function testUpdateProfileFailsNoPayload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            'v3/auth/update-profile',
            []
        );

        $response->assertStatus(400);
    }

    public function testUpdateProfileSuccess(): void
    {
        $this->createUser();
        
        $this->actingAs(User::find($this->fetchRandomUser()->id));

        $response = $this->post(
            'v3/auth/update-profile',
            [
                'name' => $this->faker->name
            ]
        );

        $response->assertStatus(204);
    }

    public function testUserSuccess(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->get('v3/auth/user');

        $response->assertStatus(200);
    }
}
