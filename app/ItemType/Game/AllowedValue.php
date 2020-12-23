<?php

declare(strict_types=1);

namespace App\ItemType\Game;

use App\ItemType\AllowedValue as BaseAllowedValue;
use App\Models\Category;

class AllowedValue extends BaseAllowedValue
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        array $viewable_resource_types
    ) {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $viewable_resource_types
        );

        $this->entity = new Item();

        $this->setAllowedValueFields();
    }

    public function fetch(): BaseAllowedValue
    {
        $this->fetchValuesForWinner();

        return $this;
    }

    protected function setAllowedValueFields(): void
    {
        $this->values = [
            'winner_id' => null,
        ];
    }

    protected function fetchValuesForWinner(): void
    {
        if (array_key_exists('winner_id', $this->available_parameters) === true) {
            $allowed_values = [];

            $winners = (new Category())->paginatedCollection(
                $this->resource_type_id,
                $this->viewable_resource_types,
                0,
                100
            );

            foreach ($winners as $winner) {
                $winner_id = $this->hash->encode('category', $winner['category_id']);

                $allowed_values[$winner_id] = [
                    'value' => $winner_id,
                    'name' => $winner['category_name'],
                    'description' => trans('item-type-'.$this->entity->type().
                            '/allowed-values.description-prefix-winner_id').
                        $winner['category_name'].
                        trans('item-type-'.$this->entity->type().
                            '/allowed-values.description-suffix-winner_id'),
                ];
            }

            $this->values['winner_id'] = ['allowed_values' => $allowed_values];
        }
    }
}
