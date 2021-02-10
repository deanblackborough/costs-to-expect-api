<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use Tests\TestCase;

class ResourceTypeManageTest extends TestCase
{
    /** @test */
    public function create_resource_type_fails_item_type_invalid(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(200),
                'description' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bg'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_no_description_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_no_name_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'description' => $this->faker->text(200),
                'item_type_id' => 'OqZwKX16bW'
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            []
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $name = $this->faker->text(10);

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $name,
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        // Create the second with the same name
        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $name,
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function create_resource_type_fails_not_signed_in(): void
    {
        $response = $this->post(
            route('resource-type.create'),
            []
        );

        $response->assertStatus(403);
    }

    /** @test */
    public function create_resource_type_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());
    }

    /** @test */
    public function update_resource_type_fails_extra_fields_in_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->patch(
            route('resource-type.update', ['resource_type_id' => $id]),
            [
                'extra' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_resource_type_fails_no_payload(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->patch(
            route('resource-type.update', ['resource_type_id' => $id]),
            [
            ]
        );

        $response->assertStatus(400);
    }

    /** @test */
    public function update_resource_type_fails_non_unique_name(): void
    {
        $this->actingAs(User::find(1));

        $name = $this->faker->text(15);

        // Create the first resource type
        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $name,
                'description' => $this->faker->text(255),
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        // Create the second resource type
        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(15),
                'description' => $this->faker->text(255),
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        // Update with same name as the first
        $response = $this->patch(
            route('resource-type.update', ['resource_type_id' => $id]),
            [
                'name' => $name
            ]
        );

        $response->assertStatus(422);
    }

    /** @test */
    public function update_resource_type_success(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->post(
            route('resource-type.create'),
            [
                'name' => $this->faker->text(255),
                'description' => $this->faker->text,
                'item_type_id' => 'OqZwKX16bW',
                'public' => false
            ]
        );

        $response->assertStatus(201);
        $this->assertJsonIsResourceType($response->content());

        $id = $response->json('id');

        $response = $this->patch(
            route('resource-type.update', ['resource_type_id' => $id]),
            [
                'name' => $this->faker->text(100)
            ]
        );

        $response->assertStatus(204);
    }
}
