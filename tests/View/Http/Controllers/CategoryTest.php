<?php

namespace Tests\View\Http\Controllers;

use App\User;
use Tests\TestCase;

final class CategoryTest extends TestCase
{
    public function testAllocatedExpenseCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryCollectionPagination(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'offset' => 0,
            'limit' => 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 2);
        $response->assertHeader('X-Offset', 0);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', "");
        $response->assertHeader('X-Link-Next', '/v3/resource-types/' . $resource_type_id . '/categories?offset=2&limit=2');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryCollectionPaginationPrevious(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'offset' => 2,
            'limit' => 2
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 1);
        $response->assertHeader('X-Offset', 2);
        $response->assertHeader('X-Limit', 2);
        $response->assertHeader('X-Link-Previous', '/v3/resource-types/' . $resource_type_id . '/categories?offset=0&limit=2');
        $response->assertHeader('X-Link-Next', '');

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryCollectionSearchDescription(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $search_string = $this->faker->text(100);

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id, ['description' => $search_string]);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'search' => 'description:' . $search_string
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'description:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);
        $this->assertEquals($search_string, $response->json()[0]['description']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryCollectionSearchName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $search_string = $this->faker->text(25);

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id, ['name' => $search_string]);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'search' => 'name:' . $search_string
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Search', 'name:' . urlencode($search_string));
        $response->assertHeader('X-Count', 1);
        $this->assertEquals($search_string, $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryCollectionSortCreated(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        sleep(2); // Ensure the created_at timestamps are different
        $this->createRandomCategory($resource_type_id, ['name' => 'created-last']);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'sort'=>'created:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'created:desc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('created-last', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryCollectionSortDescription(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id, ['description' => 'ZZZZZZZZZZZZA']);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'sort' => 'description:desc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'description:desc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('ZZZZZZZZZZZZA', $response->json()[0]['description']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }
    
    public function testAllocatedExpenseCategoryCollectionSortName(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id, ['name' => 'AAAAAAAAAAAAB']);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id,
            'sort' => 'name:asc'
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Sort', 'name:asc');
        $response->assertHeader('X-Count', 3);
        $this->assertEquals('AAAAAAAAAAAAB', $response->json()[0]['name']);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testAllocatedExpenseCategoryShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);

        $response->assertStatus(200);

        $this->assertJsonMatchesCategorySchema($response->getContent());
    }

    /** @test */
    public function allocatedExpenseCategoryShowIncludeSubcategories(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'include_subcategories' => true
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesCategorySchemaWhichIncludesSubcategories($response->getContent());
    }

    public function testBudgetCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        // This test will change later when we remove access to categories for
        // the Budget and Budget pro item types, for now it is accessible
        $resource_type_id = $this->createBudgetResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testBudgetCategoryShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);

        $response->assertStatus(200);

        $this->assertJsonMatchesCategorySchema($response->getContent());
    }

    public function testBudgetCategoryShowIncludeSubcategories(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'include_subcategories' => true
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesCategorySchemaWhichIncludesSubcategories($response->getContent());
    }

    public function testBudgetProCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        // This test will change later when we remove access to categories for
        // the Budget and Budget pro item types, for now it is accessible
        $resource_type_id = $this->createBudgetProResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testBudgetProCategoryShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);

        $response->assertStatus(200);

        $this->assertJsonMatchesCategorySchema($response->getContent());
    }

    public function testBudgetProCategoryShowIncludeSubcategories(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'include_subcategories' => true
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesCategorySchemaWhichIncludesSubcategories($response->getContent());
    }

    public function testGameCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);

        $response->assertStatus(200);
        $response->assertHeader('X-Total-Count', 3);
        $response->assertHeader('X-Count', 3);

        foreach ($response->json() as $item) {
            try {
                $json = json_encode($item, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $this->fail('Unable to encode the JSON string');
            }

            $this->assertJsonMatchesCategorySchema($json);
        }
    }

    public function testGameCategoryShow(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);

        $response->assertStatus(200);

        $this->assertJsonMatchesCategorySchema($response->getContent());
    }

    public function testGameCategoryShowIncludeSubcategories(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);
        $this->createRandomSubcategory($resource_type_id, $category_id);

        $response = $this->fetchCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id,
            'include_subcategories' => true
        ]);

        $response->assertStatus(200);
        $this->assertJsonMatchesCategorySchemaWhichIncludesSubcategories($response->getContent());
    }

    public function testOptionsRequestForAllocatedExpenseCategory(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->fetchOptionsForCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category.json');
    }

    public function testOptionsRequestForAllocatedExpenseCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createAllocatedExpenseResourceType();
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchOptionsForCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category-collection.json');
    }

    public function testOptionsRequestForBudgetCategory(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        // This test will be modified later when access to categories is
        // removed for the Budget and Budget pro item types, for now it is accessible
        $response = $this->fetchOptionsForCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category.json');
    }

    public function testOptionsRequestForBudgetCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetResourceType();
        $this->createRandomCategory($resource_type_id);

        // This test will be modified later when we disable categories for the Budget
        // and Budget pro item type, for now it is accessible
        $response = $this->fetchOptionsForCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category-collection.json');
    }

    public function testOptionsRequestForBudgetProCategory(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetProResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        // This test will be modified later when access to categories is
        // removed for the Budget and Budget pro item types, for now it is accessible
        $response = $this->fetchOptionsForCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category.json');
    }

    public function testOptionsRequestForBudgetProCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createBudgetProResourceType();
        $this->createRandomCategory($resource_type_id);

        // This test will be modified later when we disable categories for the Budget
        // and Budget pro item type, for now it is accessible
        $response = $this->fetchOptionsForCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category-collection.json');
    }

    public function testOptionsRequestForGameCategory(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $category_id = $this->createRandomCategory($resource_type_id);

        $response = $this->fetchOptionsForCategory([
            'resource_type_id' => $resource_type_id,
            'category_id' => $category_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category.json');
    }

    public function testOptionsRequestForGameCategoryCollection(): void
    {
        $this->actingAs(User::find($this->createUser()));

        $resource_type_id = $this->createGameResourceType();
        $this->createRandomCategory($resource_type_id);

        $response = $this->fetchOptionsForCategoryCollection([
            'resource_type_id' => $resource_type_id
        ]);
        $response->assertStatus(200);

        $this->assertProvidedJsonMatchesDefinedSchema($response->content(), 'api/schema/options/category-collection.json');
    }
}
