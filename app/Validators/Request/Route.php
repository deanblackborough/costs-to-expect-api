<?php
declare(strict_types=1);

namespace App\Validators\Request;

use App\Validators\Request\Routes\Category;
use App\Validators\Request\Routes\Item;
use App\Validators\Request\Routes\ItemCategory;
use App\Validators\Request\Routes\ItemSubCategory;
use App\Validators\Request\Routes\Resource;
use App\Validators\Request\Routes\ResourceType;
use App\Validators\Request\Routes\SubCategory;
use App\Utilities\Response as UtilityResponse;

/**
 * Validate the set route parameters, redirect to 404 if invalid
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Route
{
    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $category_id
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function category(
        $category_id,
        array $permitted_resource_types,
        bool $manage = false
    )
    {
        if ($manage === false) {
            if (
                Category::existsToUserForViewing(
                    (int) $category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.category'));
            }
        } else {
            if (
                Category::existsToUserForManagement(
                    (int) $category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.category'));
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $category_id
     * @param $subcategory_id
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function subcategory(
        $category_id,
        $subcategory_id,
        array $permitted_resource_types,
        $manage = false
    )
    {
        if ($manage === false) {
            if (
                SubCategory::existsToUserForViewing(
                    (int) $category_id,
                    (int) $subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.subcategory'));
            }
        } else {
            if (
                SubCategory::existsToUserForManagement(
                    (int) $category_id,
                    (int) $subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.subcategory'));
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function resourceType(
        $resource_type_id,
        array $permitted_resource_types,
        bool $manage = false
    )
    {
         if ($manage === false) {
            if (
                ResourceType::existsToUserForViewing(
                    (int) $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.resource-type'));
            }
        } else {
            if (
                ResourceType::existsToUserForManagement(
                    (int) $resource_type_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.resource-type'));
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function resource(
        $resource_type_id,
        $resource_id,
        array $permitted_resource_types,
        bool $manage = false
    )
    {
        if ($manage === false) {
            if (
                Resource::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.resource'));
            }
        } else {
            if (
                Resource::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.resource'));
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function item(
        $resource_type_id,
        $resource_id,
        $item_id,
        array $permitted_resource_types,
        bool $manage = false
    )
    {
        if ($manage === false) {
            if (
                Item::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.item'));
            }
        } else {
            if (
                Item::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.item'));
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id,
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function itemCategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        array $permitted_resource_types,
        bool $manage = false
    ) {
        if ($manage === false) {
            if (
                ItemCategory::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.item-category'));
            }
        } else {
            if (
                ItemCategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.item-category'));
            } else {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate the route, checks the route parameters based on the users
     * permitted resource types
     *
     * @param $resource_type_id
     * @param $resource_id
     * @param $item_id
     * @param $item_category_id
     * @param $item_subcategory_id
     * @param array $permitted_resource_types
     * @param bool $manage
     *
     * @return bool
     */
    static public function itemSubcategory(
        $resource_type_id,
        $resource_id,
        $item_id,
        $item_category_id,
        $item_subcategory_id,
        array $permitted_resource_types,
        bool $manage = false
    ) {
        if ($manage === false) {
            if (
                ItemSubCategory::existsToUserForViewing(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    (int) $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFound(trans('entities.item-subcategory'));
            }
        } else {
            if (
                ItemSubCategory::existsToUserForManagement(
                    (int) $resource_type_id,
                    (int) $resource_id,
                    (int) $item_id,
                    (int) $item_category_id,
                    (int) $item_subcategory_id,
                    $permitted_resource_types
                ) === false
            ) {
                UtilityResponse::notFoundOrNotAccessible(trans('entities.item-subcategory'));
            } else {
                return true;
            }
        }

        return false;
    }
}
