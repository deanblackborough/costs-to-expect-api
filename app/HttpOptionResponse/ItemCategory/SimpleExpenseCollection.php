<?php
declare(strict_types=1);

namespace App\HttpOptionResponse\ItemCategory;

use App\HttpOptionResponse\Response;
use Illuminate\Support\Facades\Config;

class SimpleExpenseCollection extends Response
{
    public function create()
    {
        $get = new \App\HttpVerb\Get();
        $this->verbs['GET'] = $get->setAuthenticationStatus($this->permissions['view'])->
            setParameters(Config::get('api.item-category.parameters'))->
            setDescription('route-descriptions.item_category_GET_index')->
            option();

        $post = new \App\HttpVerb\Post();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-category.fields-post'))->
            setDynamicFields($this->allowed_fields)->
            setAuthenticationRequirement(true)->
            setAuthenticationStatus($this->permissions['manage'])->
            setDescription('route-descriptions.item_category_POST_simple_expense')->
            option();

        return $this;
    }
}
