<?php

namespace App\Http\Controllers;

use App\Response\Cache;
use App\Request\Route;
use App\Models\ItemCategory;
use App\Models\ItemSubcategory;
use App\Models\Subcategory;
use App\Models\Transformers\ItemSubcategory as ItemSubcategoryTransformer;
use App\Request\Validate\ItemSubcategory as ItemSubcategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubcategoryManage extends Controller
{
    /**
     * Assign the sub category
     *
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function create(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        if ($item_category_id === 'nill') {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $validator = (new ItemSubcategoryValidator)->create(['category_id' => $item_category->category_id]);
        \App\Request\BodyValidation::validateAndReturnErrors(
            $validator,
            $this->fieldsData($item_category_id)
        );

        try {
            $subcategory_id = $this->hash->decode('subcategory', request()->input('subcategory_id'));

            if ($subcategory_id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $item_sub_category = new ItemSubcategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $subcategory_id
            ]);
            $item_sub_category->save();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->items($resource_type_id, $resource_id),
                $cache_key->resourceTypeItems($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->items($resource_type_id, $resource_id),
                    $cache_key->resourceTypeItems($resource_type_id)
                ]);
            }
        } catch (Exception $e) {
            \App\Response\Responses::failedToSaveModelForCreate();
        }

        return response()->json(
            (new ItemSubcategoryTransformer((new ItemSubcategory())->instanceToArray($item_sub_category)))->asArray(),
            201
        );
    }

    /**
     * Generate any conditional POST parameters, will be merged with the data
     * arrays defined in config/api/[type]/fields.php
     *
     * @param integer $category_id
     *
     * @return array
     */
    private function fieldsData($category_id): array
    {
        $sub_categories = (new Subcategory())
            ->select('id', 'name', 'description')
            ->where('category_id', '=', $category_id)
            ->get();

        $conditional_post_parameters = ['subcategory_id' => []];

        foreach ($sub_categories as $sub_category) {
            $id = $this->hash->encode('subcategory', $sub_category->id);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $conditional_post_parameters['subcategory_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $sub_category->name,
                'description' => $sub_category->description
            ];
        }

        return $conditional_post_parameters;
    }

    /**
     * Delete the assigned sub category
     *
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id,
     * @param string $item_subcategory_id
     *
     * @return JsonResponse
     */
    public function delete(
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_subcategory_id
    ): JsonResponse
    {
        Route\Validate::item(
            $resource_type_id,
            $resource_id,
            $item_id,
            $this->permitted_resource_types,
            true
        );

        $cache_control = new Cache\Control(Auth::user()->id);
        $cache_key = new Cache\Key();

        if ($item_category_id === 'nill' || $item_subcategory_id === 'nill') {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }

        $item_sub_category = (new ItemSubcategory())->instance(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_subcategory_id
        );

        if ($item_sub_category === null) {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'));
        }


        try {
            (new ItemSubcategory())->find($item_subcategory_id)->delete();

            $cache_control->clearPrivateCacheKeys([
                $cache_key->items($resource_type_id, $resource_id),
                $cache_key->resourceTypeItems($resource_type_id)
            ]);

            if (in_array($resource_type_id, $this->public_resource_types, true)) {
                $cache_control->clearPublicCacheKeys([
                    $cache_key->items($resource_type_id, $resource_id),
                    $cache_key->resourceTypeItems($resource_type_id)
                ]);
            }

            \App\Response\Responses::successNoContent();
        } catch (QueryException $e) {
            \App\Response\Responses::foreignKeyConstraintError();
        } catch (Exception $e) {
            \App\Response\Responses::notFound(trans('entities.item-subcategory'), $e);
        }
    }
}
