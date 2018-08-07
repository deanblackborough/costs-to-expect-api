<?php

namespace App\Http\Controllers;

use App\Http\Route\Validators\ItemCategory as ItemCategoryRouteValidator;
use App\Models\ItemCategory;
use App\Models\ItemSubCategory;
use App\Models\SubCategory;
use App\Transformers\ItemSubCategory as ItemSubCategoryTransformer;
use App\Validators\ItemSubCategory as ItemSubCategoryValidator;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Manage the category for an item row
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ItemSubCategoryController extends Controller
{
    /**
     * Return the sub category assigned to an item
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function index(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        if (ItemCategoryRouteValidator::validate(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        ) === false) {
            return $this->returnResourceNotFound();
        }

        $item_sub_category = (new ItemSubCategory())->paginatedCollection(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        );

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemSubCategoryTransformer($item_sub_category))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Return a single item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_sub_category_id
     *
     * @return JsonResponse
     */
    public function show(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_sub_category_id
    ): JsonResponse
    {
        if (ItemCategoryRouteValidator::validate(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id
        ) === false) {
            return $this->returnResourceNotFound();
        }

        $item_sub_category = (new ItemSubCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_sub_category_id
        );

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        $headers = [
            'X-Total-Count' => 1
        ];

        return response()->json(
            (new ItemSubCategoryTransformer($item_sub_category))->toArray(),
            200,
            $headers
        );
    }

    /**
     * Generate the OPTIONS request for the item list
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function optionsIndex(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        if (ItemCategoryRouteValidator::validate(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            ) === false) {
            return $this->returnResourceNotFound();
        }

        $allowed_values = [];
        $item_category = (new ItemCategory())->find($item_category_id);
        if ($item_category_id !== null) {
            $allowed_values = $this->allowedValues($item_category->category_id);
        }

        return $this->generateOptionsForIndex(
            'api.descriptions.item_sub_category.GET_index',
            'api.descriptions.item_sub_category.POST',
            'api.routes.item_sub_category.fields',
            'api.routes.item_sub_category.parameters',
            $allowed_values
        );
    }

    /**
     * Generate the OPTIONS request for a specific item
     *
     * @param Request $request
     * @param string $resource_id
     * @param string $resource_type_id
     * @param string $item_id
     * @param string $item_category_id
     * @param string $item_sub_category_id
     *
     * @return JsonResponse
     */
    public function optionsShow(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_sub_category_id
    ): JsonResponse
    {
        if (ItemCategoryRouteValidator::validate(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            ) === false) {
            return $this->returnResourceNotFound();
        }

        $item_sub_category = (new ItemSubCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_sub_category_id
        );

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        return $this->generateOptionsForShow(
            'api.descriptions.item_sub_category.GET_show',
            'api.descriptions.item_sub_category.DELETE',
            'api.descriptions.item_sub_category.PATCH',
            'api.routes.item_sub_category.fields'
        );
    }

    /**
     * Assign the sub category
     *
     * @param Request $request
     * @param string $resource_type_id
     * @param string $resource_id
     * @param string $item_id
     * @param string $item_category_id
     *
     * @return JsonResponse
     */
    public function create(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id
    ): JsonResponse
    {
        if (ItemCategoryRouteValidator::validate(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            ) === false) {
            return $this->returnResourceNotFound();
        }

        $item_category = (new ItemCategory())
            ->where('item_id', '=', $item_id)
            ->find($item_category_id);

        $validator = (new ItemSubCategoryValidator)->create($request, $item_category->category_id);

        if ($validator->fails() === true) {
            return $this->returnValidationErrors($validator, $this->allowedValues($item_category->category_id));
        }

        try {
            $sub_category_id = $this->hash->decode('sub_category', $request->input('sub_category_id'));

            if ($sub_category_id === false) {
                return response()->json(
                    [
                        'message' => 'Unable to decode parameter or hasher not found'
                    ],
                    500
                );
            }

            $item_sub_category = new ItemSubCategory([
                'item_category_id' => $item_category_id,
                'sub_category_id' => $sub_category_id
            ]);
            $item_sub_category->save();
        } catch (Exception $e) {
            return response()->json(
                [
                    'message' => 'Error creating new record'
                ],
                500
            );
        }

        return response()->json(
            (new ItemSubCategoryTransformer($item_sub_category))->toArray(),
            201
        );
    }

    /**
     * Generate the array of allowed values fields
     *
     * @return array
     */
    private function allowedValues($category_id)
    {
        $sub_categories = (new SubCategory())
            ->select('id', 'name', 'description')
            ->where('category_id', '=', $category_id)
            ->get();

        $allowed_values = ['sub_category_id' => []];

        foreach ($sub_categories as $sub_category) {
            $id = $this->hash->encode('sub_category', $sub_category->id);

            if ($id === false) {
                return response()->json(
                    [
                        'message' => 'Unable to encode parameter or hasher not found'
                    ],
                    500
                );
            }

            $allowed_values['sub_category_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $sub_category->name,
                'description' => $sub_category->description
            ];
        }

        return $allowed_values;
    }

    /**
     * Delete the assigned sub category
     *
     * @param Request $request,
     * @param string $resource_type_id,
     * @param string $resource_id,
     * @param string $item_id,
     * @param string $item_category_id,
     * @param string $item_sub_category_id
     *
     * @return JsonResponse
     */
    public function delete(
        Request $request,
        string $resource_type_id,
        string $resource_id,
        string $item_id,
        string $item_category_id,
        string $item_sub_category_id
    ): JsonResponse
    {
        if (ItemCategoryRouteValidator::validate(
                $resource_type_id,
                $resource_id,
                $item_id,
                $item_category_id
            ) === false) {
            return $this->returnResourceNotFound();
        }

        $item_sub_category = (new ItemSubCategory())->single(
            $resource_type_id,
            $resource_id,
            $item_id,
            $item_category_id,
            $item_sub_category_id
        );

        if ($item_sub_category === null) {
            return $this->returnResourceNotFound();
        }

        try {
            $item_sub_category->delete();

            return response()->json([],204);
        } catch (QueryException $e) {
            return $this->returnForeignKeyConstraintError();
        } catch (Exception $e) {
            return $this->returnResourceNotFound();
        }
    }
}
