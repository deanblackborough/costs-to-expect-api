<?php

namespace Tests\Feature\Http\Controllers\Summary;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Summary\SubcategoryView
 */
class SubcategoryViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get('v2/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories');

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function options_index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get('v2/summary/resource-types/{resource_type_id}/categories/{category_id}/subcategories');

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
