<?php

namespace App\Http\Controllers;

use App\Models\ItemType;
use App\Models\Resource;
use App\Option\Delete;
use App\Option\Get;
use App\Option\Patch;
use App\Option\Post;
use App\Response\Cache;
use App\Response\Header\Headers;
use App\Request\Parameter;
use App\Request\Route;
use App\Response\Pagination as UtilityPagination;
use App\Models\ResourceType;
use App\Models\Transformers\ResourceType as ResourceTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

/**
 * Manage resource types
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceTypeView extends Controller
{
    protected bool $allow_entire_collection = true;

    /**
     * Return all the resource types
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $cache_control = new Cache\Control($this->user_id);
        $cache_control->setTtlOneWeek();

        $cache_collection = new Cache\Collection();
        $cache_collection->setFromCache($cache_control->get(request()->getRequestUri()));

        if ($cache_control->cacheable() === false || $cache_collection->valid() === false) {

            $search_parameters = Parameter\Search::fetch(
                array_keys(Config::get('api.resource-type.searchable'))
            );

            $sort_parameters = Parameter\Sort::fetch(
                Config::get('api.resource-type.sortable')
            );

            $total = (new ResourceType())->totalCount(
                $this->permitted_resource_types,
                $this->include_public,
                $search_parameters
            );

            $pagination = new UtilityPagination(request()->path(), $total);
            $pagination_parameters = $pagination->allowPaginationOverride($this->allow_entire_collection)->
                setSearchParameters($search_parameters)->
                setSortParameters($sort_parameters)->
                parameters();

            $resource_types = (new ResourceType())->paginatedCollection(
                $this->permitted_resource_types,
                $this->include_public,
                $pagination_parameters['offset'],
                $pagination_parameters['limit'],
                $search_parameters,
                $sort_parameters
            );

            $collection = array_map(
                static function ($resource_type) {
                    return (new ResourceTypeTransformer($resource_type))->asArray();
                },
                $resource_types
            );

            $headers = new Headers();
            $headers->collection($pagination_parameters, count($resource_types), $total)->
                addCacheControl($cache_control->visibility(), $cache_control->ttl())->
                addETag($collection)->
                addSearch(Parameter\Search::xHeader())->
                addSort(Parameter\Sort::xHeader());

            $cache_collection->create($total, $collection, $pagination_parameters, $headers->headers());
            $cache_control->put(request()->getRequestUri(), $cache_collection->content());
        }

        return response()->json($cache_collection->collection(), 200, $cache_collection->headers());
    }

    /**
     * Return a single resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function show(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $parameters = Parameter\Request::fetch(array_keys(Config::get('api.resource-type.parameters.item')));

        $resource_type = (new ResourceType())->single(
            $resource_type_id,
            $this->permitted_resource_types,
            $this->include_public
        );

        if ($resource_type === null) {
            \App\Response\Responses::notFound(trans('entities.resource-type'));
        }

        $resources = [];
        if (
            array_key_exists('include-resources', $parameters) === true &&
            $parameters['include-resources'] === true
        ) {
            $resources = (new Resource())->paginatedCollection(
                $resource_type_id
            );
        }

        $headers = new Headers();
        $headers->item()->addParameters(Parameter\Request::xHeader());

        return response()->json(
            (new ResourceTypeTransformer($resource_type, ['resources' => $resources]))->asArray(),
            200,
            $headers->headers()
        );
    }

    /**
     * Generate the OPTIONS request for the resource type list
     *
     * @return JsonResponse
     */
    public function optionsIndex(): JsonResponse
    {
        $get = Get::init()->
            setSortable('api.resource-type.sortable')->
            setSearchable('api.resource-type.searchable')->
            setPaginationOverride(true)->
            setDescription('route-descriptions.resource_type_GET_index')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            option();

        $post = Post::init()->
            setFields('api.resource-type.fields')->
            setDynamicFields($this->fieldsData())->
            setDescription('route-descriptions.resource_type_POST')->
            setAuthenticationStatus(($this->user_id !== null) ? true : false)->
            setAuthenticationRequired(true)->
            option();

        return $this->optionsResponse(
            $get + $post,
            200
        );
    }

    /**
     * Generate the OPTIONS request fir a specific resource type
     *
     * @param string $resource_type_id
     *
     * @return JsonResponse
     */
    public function optionsShow(string $resource_type_id): JsonResponse
    {
        Route\Validate::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $permissions = Route\Permission::resourceType(
            $resource_type_id,
            $this->permitted_resource_types
        );

        $get = Get::init()->
            setParameters('api.resource-type.parameters.item')->
            setDescription('route-descriptions.resource_type_GET_show')->
            setAuthenticationStatus($permissions['view'])->
            option();

        $delete = Delete::init()->
            setDescription('route-descriptions.resource_type_DELETE')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        $patch = Patch::init()->
            setFields('api.resource-type.fields-patch')->
            setDescription('route-descriptions.resource_type_PATCH')->
            setAuthenticationRequired(true)->
            setAuthenticationStatus($permissions['manage'])->
            option();

        return $this->optionsResponse(
            $get + $delete + $patch,
            200
        );
    }

    /**
     * Generate any conditional POST parameters, will be merged with the relevant
     * config/api/[type]/fields.php data array
     *
     * @return array
     */
    private function fieldsData(): array
    {
        $item_types = (new ItemType())->minimisedCollection();

        $parameters = ['item_type_id' => []];
        foreach ($item_types as $item_type) {
            $id = $this->hash->encode('item-type', $item_type['item_type_id']);

            if ($id === false) {
                \App\Response\Responses::unableToDecode();
            }

            $parameters['item_type_id']['allowed_values'][$id] = [
                'value' => $id,
                'name' => $item_type['item_type_name'],
                'description' => $item_type['item_type_description']
            ];
        }

        return $parameters;
    }
}
