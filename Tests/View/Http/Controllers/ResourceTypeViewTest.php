<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class ResourceTypeViewTest extends TestCase
{
    /** @test */
    public function optionsRequestForResourceType(): void
    {
        $this->actingAs(User::find(1));
        $resource_type_id = $this->createAndReturnResourceTypeId();

        $response = $this->optionsResourceType(['resource_type_id' => $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonMatchesSchema($response->content(), 'api/schema/options/resource-type.json');
    }

    /** @test */
    public function optionsRequestForResourceTypeCollection(): void
    {
        $response = $this->optionsResourceTypeCollection();
        $response->assertStatus(200);

        $this->assertJsonMatchesSchema($response->content(), 'api/schema/options/resource-type-collection.json');
    }

    /** @test */
    public function resourceTypeCollection(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes();

        $response->assertStatus(200);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /** @test */
    public function resourceTypeCollectionPagination(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>1, 'limit'=> 1]);

        $response->assertStatus(200);
        $response->assertHeader('X-Offset', 1);
        $response->assertHeader('X-Limit', 1);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSearchDescription(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['search'=>'description:resource-search']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:resource-search');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSearchName(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['search'=>'name:resource-search']);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:resource-search');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSortCreated(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['sort'=>'created:asc']);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:asc');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /**
     * @test
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function resourceTypeCollectionSortName(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['sort'=>'name:asc']);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonIsResourceType($json);
        }
    }

    /** @test */
    public function resourceTypeShow(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->getResourceType(['resource_type_id'=> $resource_type_id]);
        $response->assertStatus(200);

        $this->assertJsonIsResourceType($response->content());
    }

    /** @test */
    public function resourceTypeShowWithParameterIncludePermittedUsers(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $response = $this->getResourceType([
            'resource_type_id'=> $resource_type_id,
            'include-permitted-users' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonIsResourceTypeAndIncludesPermittedUsers($response->content());
    }

    /** @test */
    public function resourceTypeShowWithParameterIncludeResource(): void
    {
        $this->actingAs(User::find(1));

        $response = $this->getResourceTypes(['offset'=>0, 'limit'=> 1]);
        $response->assertStatus(200);

        $resource_type_id = $response->json()[0]['id'];

        $resource_id = $this->createAndReturnResourceId($resource_type_id);

        $response = $this->getResourceType([
            'resource_type_id'=> $resource_type_id,
            'include-resources' => true
        ]);
        $response->assertStatus(200);

        $this->assertJsonIsResourceTypeAndIncludesResources($response->content());
    }
}