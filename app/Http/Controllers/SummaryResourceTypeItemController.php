<?php

namespace App\Http\Controllers;

use App\Option\Get;
use App\Validators\Request\Parameters;
use App\Validators\Request\Route;
use App\Models\ResourceTypeItem;
use App\Models\Transformers\ResourceTypeItemCategorySummary as ResourceTypeItemCategorySummaryTransformer;
use App\Models\Transformers\ResourceTypeItemMonthSummary as ResourceTypeItemMonthSummaryTransformer;
use App\Models\Transformers\ResourceTypeItemResourceSummary as ResourceTypeItemResourceSummaryTransformer;
use App\Models\Transformers\ResourceTypeItemSubcategorySummary as ResourceTypeItemSubcategorySummaryTransformer;
use App\Models\Transformers\ResourceTypeItemYearSummary as ResourceTypeItemYearSummaryTransformer;
use App\Utilities\General;
use App\Utilities\Response as UtilityResponse;
use App\Validators\Request\SearchParameters;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Summary for resource type items route
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class SummaryResourceTypeItemController extends Controller
{
    private $resource_type_id;
    private $include_unpublished = false;

    /**
     * Return the TCO for all the resources within the resource type
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $this->resource_type_id = $resource_type_id;

        $collection_parameters = Parameters::fetch([
            'include-unpublished',
            'resources',
            'year',
            'years',
            'month',
            'months',
            'category',
            'categories',
            'subcategory',
            'subcategories'
        ]);

        $search_parameters = SearchParameters::fetch([
            'description'
        ]);

        if (
            array_key_exists('include-unpublished', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['include-unpublished']) === true
        ) {
            $this->include_unpublished = true;
        }

        if (array_key_exists('years', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['years']) === true) {
            return $this->yearsSummary();
        } else if (
            array_key_exists('year', $collection_parameters) === true &&
            array_key_exists('category', $collection_parameters) === false &&
            array_key_exists('subcategory', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (
                array_key_exists('months', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['months']) === true
            ) {
                return $this->monthsSummary($collection_parameters['year']);
            } else {
                if (array_key_exists('month', $collection_parameters) === true) {
                    return $this->monthSummary(
                        $collection_parameters['year'],
                        $collection_parameters['month']
                    );
                } else {
                    return $this->yearSummary($collection_parameters['year']);
                }
            }
        }

        if (array_key_exists('categories', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['categories']) === true) {
            return $this->categoriesSummary();
        } else if (
            array_key_exists('category', $collection_parameters) === true &&
            array_key_exists('year', $collection_parameters) === false &&
            array_key_exists('month', $collection_parameters) === false &&
            count($search_parameters) === 0
        ) {
            if (
                array_key_exists('subcategories', $collection_parameters) === true &&
                General::booleanValue($collection_parameters['subcategories']) === true
            ) {
                return $this->subcategoriesSummary($collection_parameters['category']);
            } else {
                if (array_key_exists('subcategory', $collection_parameters) === true) {
                    return $this->subcategorySummary(
                        $collection_parameters['category'],
                        $collection_parameters['subcategory']
                    );
                } else {
                    return $this->categorySummary($collection_parameters['category']);
                }
            }
        }

        if (array_key_exists('resources', $collection_parameters) === true &&
            General::booleanValue($collection_parameters['resources']) === true) {
            return $this->resourcesSummary();
        }

        if (
            array_key_exists('category', $collection_parameters) === true ||
            array_key_exists('subcategory', $collection_parameters) === true ||
            array_key_exists('year', $collection_parameters) === true ||
            array_key_exists('month', $collection_parameters) === true ||
            count($search_parameters) > 0
        ) {
            return $this->filteredSummary(
                (array_key_exists('category', $collection_parameters) ? $collection_parameters['category'] : null),
                (array_key_exists('subcategory', $collection_parameters) ? $collection_parameters['subcategory'] : null),
                (array_key_exists('year', $collection_parameters) ? $collection_parameters['year'] : null),
                (array_key_exists('month', $collection_parameters) ? $collection_parameters['month'] : null),
                (count($search_parameters) > 0 ? $search_parameters : [])
            );
        }

        return $this->summary();
    }

    /**
     * Return the total summary for all the resources in the resource type
     *
     * @return JsonResponse
     */
    private function summary(): JsonResponse
    {
        $summary = (new ResourceTypeItem())->summary(
            $this->resource_type_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        return response()->json(
            [
                'total' => number_format(
                    $summary[0]['actualised_total'],
                    2,
                    '.',
                    ''
                )
            ],
            200,
            ['X-Total-Count' => 1]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by resource
     *
     * @return JsonResponse
     */
    private function resourcesSummary(): JsonResponse
    {
        $summary = (new ResourceTypeItem())->resourcesSummary(
            $this->resource_type_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        return response()->json(
            array_map(
                function ($resource) {
                    return (new ResourceTypeItemResourceSummaryTransformer($resource))->toArray();
                },
                $summary
            ),
            200,
            ['X-Total-Count' => count($summary)]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by year
     *
     * @return JsonResponse
     */
    private function yearsSummary(): JsonResponse
    {
        $summary = (new ResourceTypeItem())->yearsSummary(
            $this->resource_type_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        return response()->json(
            array_map(
                function ($year) {
                    return (new ResourceTypeItemYearSummaryTransformer($year))->toArray();
                },
                $summary
            ),
            200,
            ['X-Total-Count' => count($summary)]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for the requested year
     *
     * @param integer $year
     * @return JsonResponse
     */
    private function yearSummary($year): JsonResponse
    {
        $summary = (new ResourceTypeItem())->yearSummary(
            $this->resource_type_id,
            $year,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        return response()->json(
            (new ResourceTypeItemYearSummaryTransformer($summary[0]))->toArray(),
            200,
            ['X-Total-Count' => count($summary)]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by year
     *
     * @param integer $year
     *
     * @return JsonResponse
     */
    private function monthsSummary(int $year): JsonResponse
    {
        $summary = (new ResourceTypeItem())->monthsSummary(
            $this->resource_type_id,
            $year,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        return response()->json(
            array_map(
                function ($month) {
                    return (new ResourceTypeItemMonthSummaryTransformer($month))->toArray();
                },
                $summary
            ),
            200,
            ['X-Total-Count' => count($summary)]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for a specific month
     *
     * @param integer $year
     * @param integer $month
     *
     * @return JsonResponse
     */
    private function monthSummary(int $year, int $month): JsonResponse
    {
        $summary = (new ResourceTypeItem())->monthSummary(
            $this->resource_type_id,
            $year,
            $month,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        return response()->json(
            (new ResourceTypeItemMonthSummaryTransformer($summary[0]))->toArray(),
            200,
            ['X-Total-Count' => 1]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * grouped by category
     *
     * @return JsonResponse
     */
    private function categoriesSummary(): JsonResponse
    {
        $summary = (new ResourceTypeItem())->categoriesSummary(
            $this->resource_type_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        return response()->json(
            array_map(
                function ($category) {
                    return (new ResourceTypeItemCategorySummaryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            ['X-Total-Count' => count($summary)]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for a specific category
     *
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    private function categorySummary(int $category_id): JsonResponse
    {
        $summary = (new ResourceTypeItem())->categorySummary(
            $this->resource_type_id,
            $category_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        return response()->json(
            (new ResourceTypeItemCategorySummaryTransformer($summary[0]))->toArray(),
            200,
            ['X-Total-Count' => 1]
        );
    }

    /**
     * Return a filtered summary
     *
     * @param int|null $category_id
     * @param int|null $subcategory_id
     * @param int|null $year
     * @param int|null $month
     * @param array $search_parameters
     *
     * @return JsonResponse
     */
    public function filteredSummary(
        int $category_id = null,
        int $subcategory_id = null,
        int $year = null,
        int $month = null,
        array $search_parameters = []
    ): JsonResponse
    {
        $summary = (new ResourceTypeItem())->filteredSummary(
            $this->resource_type_id,
            $category_id,
            $subcategory_id,
            $year,
            $month,
            $search_parameters,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        return response()->json(
            [
                'total' => number_format($summary[0]['total'], 2, '.', '')
            ],
            200,
            ['X-Total-Count' => 1]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * and category grouped by subcategory
     *
     * @param integer $category_id
     *
     * @return JsonResponse
     */
    private function subcategoriesSummary(int $category_id): JsonResponse
    {
        $summary = (new ResourceTypeItem())->subcategoriesSummary(
            $this->resource_type_id,
            $category_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent(true);
        }

        return response()->json(
            array_map(
                function ($category) {
                    return (new ResourceTypeItemSubcategorySummaryTransformer($category))->toArray();
                },
                $summary
            ),
            200,
            ['X-Total-Count' => count($summary)]
        );
    }

    /**
     * Return the total summary for all the resources in the resource type
     * for a specific category and subcategory
     *
     * @param integer $category_id
     * @param integer $subcategory_id
     *
     * @return JsonResponse
     */
    private function subcategorySummary(int $category_id, int $subcategory_id): JsonResponse
    {
        $summary = (new ResourceTypeItem())->subcategorySummary(
            $this->resource_type_id,
            $category_id,
            $subcategory_id,
            $this->include_unpublished
        );

        if (count($summary) === 0) {
            UtilityResponse::successEmptyContent();
        }

        return response()->json(
            (new ResourceTypeItemSubcategorySummaryTransformer($summary[0]))->toArray(),
            200,
            ['X-Total-Count' => 1]
        );
    }


    /**
     * Generate the OPTIONS request for items summary route
     *
     * @param Request $request
     * @param string $resource_type_id
     *
     * @return JsonResponse
     *
     */
    public function optionsIndex(Request $request, string $resource_type_id): JsonResponse
    {
        Route::resourceTypeRoute(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setDescription('route-descriptions.summary-resource-type-item-GET-index')->
            setSearchable('api.resource-type-item.searchable')->
            setParameters('api.resource-type-item.summary-parameters.collection')->
            option();

        return $this->optionsResponse($get, 200);
    }
}
