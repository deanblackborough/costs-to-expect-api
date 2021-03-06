<?php
declare(strict_types=1);

namespace App\Option;

class ItemCollection extends Response
{
    public function create()
    {
        $get = new \App\Method\GetRequest();
        $this->verbs['GET'] = $get->setSortableParameters($this->entity->sortParameters())
            ->setSearchableParameters($this->entity->searchParameters())
            ->setFilterableParameters($this->entity->filterParameters())
            ->setParameters($this->entity->requestParameters())
            ->setDynamicParameters($this->allowed_parameters)
            ->setPaginationStatus(true)
            ->setAuthenticationStatus($this->permissions['view'])
            ->setDescription('route-descriptions.item_GET_index')
            ->option();

        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields($this->entity->postFields())
            ->setDescription( 'route-descriptions.item_POST')
            ->setAuthenticationRequirement(true)
            ->setAuthenticationStatus($this->permissions['manage'])
            ->setDynamicFields($this->allowed_fields)
            ->option();

        return $this;
    }
}
