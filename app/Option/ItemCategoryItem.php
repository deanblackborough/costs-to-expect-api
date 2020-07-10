<?php
declare(strict_types=1);

namespace App\Option;

class ItemCategoryItem extends Response
{
    public function create()
    {
        $get = new \App\Option\Method\Get();
        $this->verbs['GET'] = $get->setParameters('api.item-category.parameters.item')->
            setAuthenticationStatus($this->permissions['view'])->
            setDescription('route-descriptions.item_category_GET_show')->
            option();

        $delete = new \App\Option\Method\Delete();
        $this->verbs['DELETE'] = $delete->setDescription('route-descriptions.item_category_DELETE')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}
