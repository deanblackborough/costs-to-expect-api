<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class AuthenticationTest extends TestCase
{
    public function testOptionsRequestForCreatePassword(): void
    {
        $response = $this->fetchOptionsForCreatePassword();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/create-password.json');
    }

    public function testOptionsRequestForMigrateBudgetProRequestDelete(): void
    {
        $response = $this->fetchOptionsForMigrateBudgetProRequestDelete();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/migrate-budget-pro-request-delete.json');
    }

    public function testOptionsRequestForRegister(): void
    {
        $response = $this->fetchOptionsForRegister();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/register.json');
    }

    public function testOptionsRequestForUpdatePassword(): void
    {
        $response = $this->fetchOptionsForUpdatePassword();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/update-password.json');
    }

    public function testOptionsRequestForUpdateProfile(): void
    {
        $response = $this->fetchOptionsForUpdateProfile();
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/auth/options/update-profile.json');
    }
}
