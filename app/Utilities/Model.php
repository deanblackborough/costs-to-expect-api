<?php
declare(strict_types=1);

namespace App\Utilities;

/**
 * Model helper class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Model
{
    public static function applySearch(
        $collection,
        string $table,
        array $search_parameters = []
    )
    {
        if (count($search_parameters) > 0) {
            foreach ($search_parameters as $field => $search_term) {
                $collection->where($table . '.' . $field, 'LIKE', '%' . $search_term . '%');
            }
        }

        return $collection;
    }

    public static function applyResourceTypeCollectionCondition(
        $collection,
        array $permitted_resource_types,
        bool $include_public
    )
    {
        $collection->where(function ($collection) use ($permitted_resource_types, $include_public) {
            $collection->where('resource_type.public', '=', (int) $include_public)->
                orWhereIn('resource_type.id', $permitted_resource_types);
        });

        return $collection;
    }
}
