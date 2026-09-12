<?php

namespace Tests\View\Http\Controllers;

use Tests\TestCase;

final class SummaryItemTest extends TestCase
{
    /** @test */
    public function summaryItem(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);

        $response->assertStatus(200);
        $this->assertEquals('GBP', $response->json()[0]['currency']['code']);
        $this->assertEquals(2, $response->json()[0]['count']);
        $this->assertEquals('150.00', $response->json()[0]['subtotal']);
    }

    /** @test */
    public function summaryItemByYears(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2023-06-01']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-06-01']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'years' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(2, $response->json());
    }

    /** @test */
    public function summaryItemByYearAndMonth(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00', 'effective_date' => '2024-06-15']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '25.00', 'effective_date' => '2024-06-20']);
        $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '50.00', 'effective_date' => '2024-11-02']);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'year' => 2024,
            'month' => 6
        ]);

        $response->assertStatus(200);
        $this->assertEquals('June', $response->json('month'));
        $this->assertEquals(2, $response->json('subtotals.0.count'));
        $this->assertEquals('125.00', $response->json('subtotals.0.subtotal'));
    }

    /** @test */
    public function summaryItemByCategories(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $item_id = $this->quickCreateAllocatedExpenseItem($resource_type_id, $resource_id, ['total' => '100.00']);
        $category_id = $this->quickCreateRandomCategory($resource_type_id);
        $this->quickCreateItemCategory($resource_type_id, $resource_id, $item_id, $category_id);

        $response = $this->getToSummaryItemList([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id,
            'categories' => 'true'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json());
        $this->assertEquals('100.00', $response->json()[0]['subtotals'][0]['subtotal']);
    }

    /** @test */
    public function optionsRequestForSummaryItemCollection(): void
    {
        $this->actingAs($this->createUser());

        $resource_type_id = $this->quickCreateAllocatedExpenseResourceType();
        $resource_id = $this->quickCreateAllocatedExpenseResource($resource_type_id);

        $response = $this->fetchOptionsForSummaryItemCollection([
            'resource_type_id' => $resource_type_id,
            'resource_id' => $resource_id
        ]);
        $response->assertStatus(200);
    }
}
