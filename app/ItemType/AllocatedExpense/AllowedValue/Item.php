<?php
declare(strict_types=1);

namespace App\ItemType\AllocatedExpense\AllowedValue;

use App\ItemType\AllowedValue;
use App\ItemType\Entity;
use App\Models\Category;
use App\Models\Currency;
use App\Models\Subcategory;
use App\Response\Responses;

class Item extends AllowedValue
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types
    )
    {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $viewable_resource_types
        );

        $this->entity = new \App\ItemType\AllocatedExpense\Item();

        $this->setAllowedValueFields();
    }

    public function fetch(): AllowedValue
    {
        $this->fetchValuesForYear();

        $this->fetchValuesForMonth();

        $this->fetchValuesForCategory();

        $this->fetchValuesForSubcategory();

        $this->fetchValuesForCurrency();

        return $this;
    }

    protected function fetchValuesForCategory(): void
    {
        if (array_key_exists('category', $this->available_parameters) === true) {

            $allowed_values = [];

            $categories = (new Category())->paginatedCollection(
                $this->resource_type_id,
                $this->viewable_resource_types,
                0,
                100
            );

            foreach ($categories as $category) {
                $category_id = $this->hash->encode('category', $category['category_id']);

                $allowed_values[$category_id] = [
                    'value' => $category_id,
                    'name' => $category['category_name'],
                    'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-category') .
                        $category['category_name'] .
                        trans('item-type-allocated-expense/allowed-values.description-suffix-category')
                ];
            }

            $this->values['category'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForCurrency(): void
    {
        $allowed_values = [];

        $currencies = (new Currency())->minimisedCollection();

        foreach ($currencies as $currency) {
            $id = $this->hash->encode('currency', $currency['currency_id']);

            if ($id === false) {
                Responses::unableToDecode();
            }

            $allowed_values[$id] = [
                'value' => $id,
                'name' => $currency['currency_name'],
                'description' => $currency['currency_name']
            ];
        }

        $this->values['currency_id'] = ['allowed_values' => $allowed_values];
    }

    protected function fetchValuesForMonth(): void
    {
        if (array_key_exists('month', $this->available_parameters) === true) {

            $allowed_values = [];

            for ($i = 1; $i < 13; $i++) {
                $allowed_values[$i] = [
                    'value' => $i,
                    'name' => date("F", mktime(0, 0, 0, $i, 10)),
                    'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-month') .
                        date("F", mktime(0, 0, 0, $i, 1))
                ];
            }

            $this->values['month'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForSubcategory(): void
    {
        if (
            array_key_exists('category', $this->available_parameters) === true &&
            array_key_exists('subcategory', $this->available_parameters) === true &&
            array_key_exists('category', $this->defined_parameters) === true &&
            $this->defined_parameters['category'] !== null
        ) {

            $allowed_values = [];

            $subcategories = (new Subcategory())->paginatedCollection(
                $this->resource_type_id,
                (int) $this->defined_parameters['category']
            );

            foreach ($subcategories as $subcategory) {
                $subcategory_id = $this->hash->encode('subcategory', $subcategory['subcategory_id']);

                $allowed_values[$subcategory_id] = [
                    'value' => $subcategory_id,
                    'name' => $subcategory['subcategory_name'],
                    'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-subcategory') .
                        $subcategory['subcategory_name'] . trans('item-type-' . $this->entity->type() . '/allowed-values.description-suffix-subcategory')
                ];
            }

            $this->values['subcategory'] = ['allowed_values' => $allowed_values];
        }
    }

    protected function fetchValuesForYear(): void
    {
        if (array_key_exists('year', $this->available_parameters) === true) {

            $min_year = null;
            $max_year = null;

            $item_type = Entity::itemType($this->resource_type_id);
            switch ($item_type) {
                case 'allocated-expense':
                    $min_year = $this->range_limits->minimumYearByResourceTypeAndResource(
                        $this->resource_type_id,
                        $this->resource_id,
                        'item_type_allocated_expense',
                        'effective_date'
                    );
                    $max_year = $this->range_limits->maximumYearByResourceTypeAndResource(
                        $this->resource_type_id,
                        $this->resource_id,
                        'item_type_allocated_expense',
                        'effective_date'
                    );
                    break;
                default:
                    // Do nothing
                    break;
            }

            $allowed_values = [];

            if ($min_year !== null && $max_year !== null) {
                for ($i = $min_year; $i <= $max_year; $i++) {
                    $allowed_values[$i] = [
                        'value' => $i,
                        'name' => $i,
                        'description' => trans('item-type-allocated-expense/allowed-values.description-prefix-year') . $i
                    ];
                }
            }

            $this->values['year'] = ['allowed_values' => $allowed_values];

        }
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [
            'year' => null,
            'month' => null,
            'category' => null,
            'subcategory' => null,
            'currency_id' => null
        ];
    }
}
