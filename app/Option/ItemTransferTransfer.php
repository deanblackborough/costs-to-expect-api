<?php
declare(strict_types=1);

namespace App\Option;

use Illuminate\Support\Facades\Config;

class ItemTransferTransfer extends Response
{
    public function create()
    {
        $post = new \App\Method\PostRequest();
        $this->verbs['POST'] = $post->setFields(Config::get('api.item-transfer.fields'))->
            setDynamicFields($this->allowed_values)->
            setDescription('route-descriptions.item_transfer_POST')->
            setAuthenticationStatus($this->permissions['manage'])->
            setAuthenticationRequirement(true)->
            option();

        return $this;
    }
}