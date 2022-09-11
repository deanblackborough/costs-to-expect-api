<?php

declare(strict_types=1);

namespace App\HttpOptionResponse\Item;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config as LaravelConfig;

class Game extends Response
{
    public function create()
    {
        $base_path = 'api.item-type-game';

        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setParameters(LaravelConfig::get($base_path . '.parameters-show', []))
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_game_GET_show')
            ->option();

        $delete = new \App\HttpVerb\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_game_DELETE')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->option();

        $patch = new \App\HttpVerb\Patch();
        $this->verbs['PATCH'] = $patch->setFields(LaravelConfig::get($base_path . '.fields-patch', []))
            ->setDescription('route-descriptions.item_game_PATCH')
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setAuthenticationRequirement(true)
            ->setAllowedValuesForFields($this->allowed_values_for_fields)
            ->option();

        return $this;
    }
}
