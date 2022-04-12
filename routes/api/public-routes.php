<?php

use App\Http\Controllers\PermittedUserView;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'prefix' => Config::get('api.app.version.prefix'),
        'middleware' => [
            'convert.route.parameters',
            'convert.get.parameters'
        ]
    ],
    static function () {
        // Root of the API and CHANGELOG
        Route::get(
            '',
            'IndexView@index'
        );

        Route::options(
            '',
            'IndexView@optionsIndex'
        );

        Route::get(
            'changelog',
            [\App\Http\Controllers\IndexView::class, 'changeLog']
        );

        Route::options(
            'changelog',
            'IndexView@optionsChangeLog'
        );

        Route::get(
            'currencies',
            'CurrencyView@index'
        );

        Route::options(
            'currencies',
            'CurrencyView@optionsIndex'
        );

        Route::get(
            'currencies/{currency_id}',
            'CurrencyView@show'
        );

        Route::options(
            'currencies/{currency_id}',
            'CurrencyView@optionsShow'
        );

        Route::get(
            'item-types',
            'ItemTypeView@index'
        );

        Route::options(
            'item-types',
            'ItemTypeView@optionsIndex'
        );

        Route::get(
            'item-types/{item_type_id}',
            'ItemTypeView@show'
        )->name('item-type.show');

        Route::options(
            'item-types/{item_type_id}',
            'ItemTypeView@optionsShow'
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes',
            'ItemSubtypeView@index'
        );

        Route::options(
            'item-types/{item_type_id}/item-subtypes',
            'ItemSubtypeView@optionsIndex'
        );

        Route::get(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            'ItemSubtypeView@show'
        );

        Route::options(
            'item-types/{item_type_id}/item-subtypes/{item_subtype_id}',
            'ItemSubtypeView@optionsShow'
        );

        Route::get(
            'queue',
            'QueueView@index'
        );

        Route::options(
            'queue',
            'QueueView@optionsIndex'
        );

        Route::get(
            'queue/{queue_id}',
            'QueueView@show'
        );

        Route::options(
            'queue/{queue_id}',
            'QueueView@optionsShow'
        );

        Route::get(
            'resource-types',
            [\App\Http\Controllers\ResourceTypeView::class, 'index']
        )->name('resource-type.list');

        Route::options(
            'resource-types',
            [\App\Http\Controllers\ResourceTypeView::class, 'optionsIndex']
        )->name('resource-type.list.options');

        Route::get(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\ResourceTypeView::class, 'show']
        )->name('resource-type.show');

        Route::options(
            'resource-types/{resource_type_id}',
            [\App\Http\Controllers\ResourceTypeView::class, 'optionsShow']
        )->name('resource-type.show.options');

        Route::get(
            'resource-types/{resource_type_id}/categories',
            'CategoryView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories',
            'CategoryView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}',
            'CategoryView@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}',
            'CategoryView@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            [\App\Http\Controllers\SubcategoryView::class, 'index']
        )->name('subcategory.list');

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories',
            'SubcategoryView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            'SubcategoryView@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/categories/{category_id}/subcategories/{subcategory_id}',
            'SubcategoryView@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/items',
            'ResourceTypeItemView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/items',
            'ResourceTypeItemView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers',
            'ItemPartialTransferView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers',
            'ItemPartialTransferView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            'ItemPartialTransferView@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/partial-transfers/{item_partial_transfer_id}',
            'ItemPartialTransferView@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserView::class, 'index']
        )->name('permitted-user.list');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users',
            [PermittedUserView::class, 'optionsIndex']
        )->name('permitted-user.options');

        Route::get(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserView::class, 'show']
        )->name('permitted-user.show');

        Route::options(
            'resource-types/{resource_type_id}/permitted-users/{permitted_user_id}',
            [PermittedUserView::class, 'optionsShow']
        )->name('permitted-user.show.options');

        Route::get(
            'resource-types/{resource_type_id}/resources',
            [\App\Http\Controllers\ResourceView::class, 'index']
        )->name('resource.list');

        Route::options(
            'resource-types/{resource_type_id}/resources',
            [\App\Http\Controllers\ResourceView::class, 'optionsIndex']
        )->name('resource.list.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [\App\Http\Controllers\ResourceView::class, 'show']
        )->name('resource.show');

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}',
            [\App\Http\Controllers\ResourceView::class, 'optionsShow']
        )->name('resource.show.options');

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            'ItemView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items',
            'ItemView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            [\App\Http\Controllers\ItemView::class, 'show']
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}',
            'ItemView@optionsShow'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/partial-transfer',
            'ItemPartialTransferView@optionsTransfer'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/transfer',
            'ItemTransferView@optionsTransfer'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            'ItemCategoryView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories',
            'ItemCategoryView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            'ItemCategoryView@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}',
            'ItemCategoryView@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            'ItemSubcategoryView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories',
            'ItemSubcategoryView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            'ItemSubcategoryView@show'
        );

        Route::options(
            'resource-types/{resource_type_id}/resources/{resource_id}/items/{item_id}/categories/{item_category_id}/subcategories/{item_subcategory_id}',
            'ItemSubcategoryView@optionsShow'
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers',
            'ItemTransferView@optionsIndex'
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers',
            'ItemTransferView@index'
        );

        Route::options(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            'ItemTransferView@optionsShow'
        );

        Route::get(
            'resource-types/{resource_type_id}/transfers/{item_transfer_id}',
            'ItemTransferView@show'
        );

        // Request access and error logs
        Route::options(
            'request/error-log',
            'RequestView@optionsErrorLog'
        );

        Route::get(
            'request/error-log',
            'RequestView@errorLog'
        );

        Route::post(
            'request/error-log',
            'RequestManage@createErrorLog'
        );

        Route::options(
            'tools/cache',
            'ToolView@optionsCache'
        );
    }
);
