<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'item-type-allocated-expense/fields.title-name',
        'description' => 'item-type-allocated-expense/fields.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'item-type-allocated-expense/fields.title-description',
        'description' => 'item-type-allocated-expense/fields.description-description',
        'type' => 'string',
        'required' => false
    ],
    'effective_date' => [
        'field' => 'effective_date',
        'title' => 'item-type-allocated-expense/fields.title-effective_date',
        'description' => 'item-type-allocated-expense/fields.description-effective_date',
        'type' => 'date (yyyy-mm-dd)',
        'required' => true
    ],
    'publish_after' => [
        'field' => 'publish_after',
        'title' => 'item-type-allocated-expense/fields.title-publish_after',
        'description' => 'item-type-allocated-expense/fields.description-publish_after',
        'type' => 'date (yyyy-mm-dd)',
        'required' => false
    ],
    'currency_id' => [
        'field' => 'currency_id',
        'title' => 'item-type-allocated-expense/fields.title-currency_id',
        'description' => 'item-type-allocated-expense/fields.description-currency_id',
        'type' => 'string',
        'validation' => [
            'length' => 10
        ],
        'required' => true
    ],
    'total' => [
        'field' => 'total',
        'title' => 'item-type-allocated-expense/fields.title-total',
        'description' => 'item-type-allocated-expense/fields.description-total',
        'type' => 'decimal string (13,2)',
        'required' => true
    ],
    'percentage' => [
        'field' => 'percentage',
        'title' => 'item-type-allocated-expense/fields.title-percentage',
        'description' => 'item-type-allocated-expense/fields.description-percentage',
        'type' => 'integer',
        'validation' => [
            'min' => 1,
            'max' => 100
        ],
        'required' => false
    ]
];
