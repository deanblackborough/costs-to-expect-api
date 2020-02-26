<?php
declare(strict_types=1);

namespace App\Utilities;

use App\Validators\Routes\Category;
use App\Validators\Routes\Item;
use App\Validators\Routes\ItemCategory;
use App\Validators\Routes\ItemSubcategory;
use App\Validators\Routes\Resource;
use App\Validators\Routes\ResourceType;
use App\Validators\Routes\Subcategory;

/**
 * Work out the permissions for each route, permissions are read and manage
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class RoutePermission
{
    /**
     * Returns the `read` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param integer $resource_type_id
     * @param integer $category_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function category(
        $resource_type_id,
        $category_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Category::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $category_id,
                $permitted_resource_types
            ),
            'manage' => Category::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $category_id,
                $permitted_resource_types
            )
        ];
    }


    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $category_id
     * @param $subcategory_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function subcategory(
        $resource_type_id,
        $category_id,
        $subcategory_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Subcategory::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $category_id,
                (int) $subcategory_id,
                $permitted_resource_types
            ),
            'manage' => Subcategory::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $category_id,
                (int) $subcategory_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function resourceType(
        $resource_type_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => ResourceType::existsToUserForViewing(
                (int) $resource_type_id,
                $permitted_resource_types
            ),
            'manage' => ResourceType::existsToUserForManagement(
                (int) $resource_type_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function resource(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Resource::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                $permitted_resource_types
            ),
            'manage' => Resource::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function item(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => Item::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                $permitted_resource_types
            ),
            'manage' => Item::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id,
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function itemCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        array $permitted_resource_types
    ): array
    {
        return [
            'view' => ItemCategory::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                $permitted_resource_types
            ),
            'manage' => ItemCategory::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                $permitted_resource_types
            )
        ];
    }

    /**
     * Returns the `view` and `manage` permission for the current user, checks
     * against their permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id
     * @param $item_subcategory_id
     * @param array $permitted_resource_types
     *
     * @return array Two indexes, view and manage, values for both boolean
     */
    static public function itemSubcategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_subcategory_id,
        array $permitted_resource_types
    ): array
    {
        return [
        'view' => ItemSubcategory::existsToUserForViewing(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                (int) $item_subcategory_id,
                $permitted_resource_types
            ),
        'manage' => ItemSubcategory::existsToUserForManagement(
                (int) $resource_type_id,
                (int) $resource_id,
                (int) $item_id,
                (int) $item_category_id,
                (int) $item_subcategory_id,
                $permitted_resource_types
            )
        ];
    }
}
