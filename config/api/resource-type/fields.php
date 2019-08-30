<?php

declare(strict_types=1);

return [
    'name' => [
        'field' => 'name',
        'title' => 'resource-type/fields.title-name',
        'description' => 'resource-type/fields.description-name',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'description' => [
        'field' => 'description',
        'title' => 'resource-type/fields.title-description',
        'description' => 'resource-type/fields.description-description',
        'type' => 'string',
        'validation' => [
            'max-length' => 255
        ],
        'required' => true
    ],
    'private' => [
        'field' => 'private',
        'title' => 'resource-type/fields.title-private',
        'description' => 'resource-type/fields.description-private',
        'type' => 'boolean',
        'required' => true
    ]
];
