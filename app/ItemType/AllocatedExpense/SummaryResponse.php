<?php

namespace App\ItemType\AllocatedExpense;

use App\ItemType\SummaryResponse as BaseSummaryResponse;
use App\Request\Validate\Boolean;
use App\Response\Cache;
use Illuminate\Http\JsonResponse;

class SummaryResponse extends BaseSummaryResponse
{
    public function __construct(
        int $resource_type_id,
        int $resource_id,
        bool $permitted_user = false,
        int $user_id = null
    )
    {
        parent::__construct(
            $resource_type_id,
            $resource_id,
            $permitted_user,
            $user_id
        );

        $this->model = new SummaryModel();

        $this->fetchAllRequestParameters(new Item());

        $this->removeDecisionParameters();
    }

    public function response(): JsonResponse
    {
        if ($this->decision_parameters['years'] === true) {
            return $this->yearsSummary();
        }

        if (
            $this->decision_parameters['year'] !== null &&
            $this->decision_parameters['category'] === null &&
            $this->decision_parameters['subcategory'] === null &&
            count($this->search_parameters) === 0
        ) {
            if ($this->decision_parameters['months'] === true) {
                return $this->monthsSummary();
            }

            if ($this->decision_parameters['month'] !== null) {
                return $this->monthSummary();
            }

            return $this->yearSummary();
        }

        if ($this->decision_parameters['categories'] === true) {
            return $this->categoriesSummary();
        }

        if (
            $this->decision_parameters['category'] !== null &&
            $this->decision_parameters['year'] === null &&
            $this->decision_parameters['month'] === null &&
            count($this->search_parameters) === 0
        ) {
            if ($this->decision_parameters['subcategories'] === true) {
                return $this->subcategoriesSummary();
            }

            if ($this->decision_parameters['subcategory'] !== null) {
                return $this->subcategorySummary();
            }

            return $this->categorySummary();
        }

        if (
            $this->decision_parameters['category'] !== null ||
            $this->decision_parameters['subcategory'] !== null ||
            $this->decision_parameters['year'] !== null ||
            $this->decision_parameters['month'] !== null ||
            count($this->search_parameters) > 0 ||
            count($this->filter_parameters) > 0
        ) {
            return $this->filteredSummary();
        }

        return $this->summary();
    }

    protected function categoriesSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->categoriesSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->parameters
            );

            $collection = (new SummaryTransformerByCategory($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function categorySummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->categorySummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->parameters
            );

            $collection = (new SummaryTransformerByCategory($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function filteredSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->filteredSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->decision_parameters['subcategory'],
                $this->decision_parameters['year'],
                $this->decision_parameters['month'],
                $this->parameters,
                $this->search_parameters,
                $this->filter_parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new SummaryTransformer($subtotal))->asArray();
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function monthsSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->monthsSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['year'],
                $this->parameters
            );

            $collection = (new SummaryTransformerByMonth($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function monthSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->monthSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['year'],
                $this->decision_parameters['month'],
                $this->parameters
            );

            $collection = (new SummaryTransformerByMonth($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function removeDecisionParameters(): void
    {
        $this->decision_parameters['years'] = false;
        $this->decision_parameters['months'] = false;
        $this->decision_parameters['categories'] = false;
        $this->decision_parameters['subcategories'] = false;
        $this->decision_parameters['year'] = null;
        $this->decision_parameters['month'] = null;
        $this->decision_parameters['category'] = null;
        $this->decision_parameters['subcategory'] = null;

        if (array_key_exists('years', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['years']) === true) {
            $this->decision_parameters['years'] = true;
        }

        if (array_key_exists('months', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['months']) === true) {
            $this->decision_parameters['months'] = true;
        }

        if (array_key_exists('categories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['categories']) === true) {
            $this->decision_parameters['categories'] = true;
        }

        if (array_key_exists('subcategories', $this->parameters) === true &&
            Boolean::convertedValue($this->parameters['subcategories']) === true) {
            $this->decision_parameters['subcategories'] = true;
        }

        if (array_key_exists('year', $this->parameters) === true) {
            $this->decision_parameters['year'] = (int) $this->parameters['year'];
        }

        if (array_key_exists('month', $this->parameters) === true) {
            $this->decision_parameters['month'] = (int) $this->parameters['month'];
        }

        if (array_key_exists('category', $this->parameters) === true) {
            $this->decision_parameters['category'] = (int) $this->parameters['category'];
        }

        if (array_key_exists('subcategory', $this->parameters) === true) {
            $this->decision_parameters['subcategory'] = (int) $this->parameters['subcategory'];
        }

        unset(
            $this->parameters['years'],
            $this->parameters['year'],
            $this->parameters['months'],
            $this->parameters['month'],
            $this->parameters['categories'],
            $this->parameters['category'],
            $this->parameters['subcategories'],
            $this->parameters['subcategory']
        );
    }

    protected function subcategoriesSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->subCategoriesSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->parameters
            );

            $collection = (new SummaryTransformerBySubcategory($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function subcategorySummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->subCategorySummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['category'],
                $this->decision_parameters['subcategory'],
                $this->parameters
            );

            $collection = (new SummaryTransformerBySubcategory($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function summary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->summary(
                $this->resource_type_id,
                $this->resource_id,
                $this->parameters
            );

            $collection = [];
            foreach ($summary as $subtotal) {
                $collection[] = (new SummaryTransformer($subtotal))->asArray();
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function yearsSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->yearsSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->parameters
            );

            $collection = (new SummaryTransformerByYear($summary))->asArray();

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }

    protected function yearSummary(): JsonResponse
    {
        $cache_control = new Cache\Control(
            $this->permitted_user,
            $this->user_id
        );

        if ($cache_control->visibility() === 'public') {
            $cache_control->setTtlOneWeek();
        } else {
            $cache_control->setTtlOneDay();
        }

        $cache_summary = new Cache\Summary();
        $cache_summary->setFromCache($cache_control->getByKey(request()->getRequestUri()));

        if ($cache_control->isRequestCacheable() === false || $cache_summary->valid() === false) {

            $summary = $this->model->yearSummary(
                $this->resource_type_id,
                $this->resource_id,
                $this->decision_parameters['year'],
                $this->parameters
            );

            $collection = (new SummaryTransformerByYear($summary))->asArray();

            if (count($collection) === 1) {
                $collection = $collection[0];
            } else {
                $collection = [];
            }

            $this->assignToCache(
                $summary,
                $collection,
                $cache_control,
                $cache_summary
            );
        }

        return response()->json($cache_summary->collection(), 200, $cache_summary->headers());
    }
}
