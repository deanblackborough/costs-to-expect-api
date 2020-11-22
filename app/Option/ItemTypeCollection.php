<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ItemTypeCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters(Config::get('api.item-type.sortable'))->
            setSearchableParameters(Config::get('api.item-type.searchable'))->
            setPaginationStatus(true, true)->
            setDescription('route-descriptions.item_type_GET_index')->
            setAuthenticationStatus($this->permissions['view'])->
            option();

        return $this;
    }
}
