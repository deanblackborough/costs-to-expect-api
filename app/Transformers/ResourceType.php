<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Transformers\Resource as ResourceTransformer;

/**
 * Transform the data from our queries into the format we want to display
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ResourceType extends Transformer
{
    public function format(array $to_transform): void
    {
        $data = null;

        try {
            if (array_key_exists('resource_type_data', $to_transform) && $to_transform['resource_type_data'] !== null) {
                $data = json_decode($to_transform['resource_type_data'], true, 512, JSON_THROW_ON_ERROR);
            }
        } catch (\JsonException $e) {
            $data = [
                'error' => 'Unable to decode data'
            ];
        }

        $this->transformed = [
            'id' => $this->hash->resourceType()->encode($to_transform['resource_type_id']),
            'name' => $to_transform['resource_type_name'],
            'description' => $to_transform['resource_type_description'],
            'data' => $data,
            'created' => $to_transform['resource_type_created_at'],
            'public' => (bool) $to_transform['resource_type_public'],
        ];

        if (
            array_key_exists('resource_type_item_type_id', $to_transform) === true &&
            array_key_exists('resource_type_item_type_name', $to_transform) === true &&
            array_key_exists('resource_type_item_type_description', $to_transform) === true
        ) {
            $this->transformed['item_type'] = [
                'id' => $this->hash->itemType()->encode($to_transform['resource_type_item_type_id']),
                'name' => $to_transform['resource_type_item_type_name'],
                'friendly_name' => $to_transform['resource_type_item_type_friendly_name'],
                'description' => $to_transform['resource_type_item_type_description']
            ];
        }

        if (array_key_exists('resource_type_resources', $to_transform)) {
            $this->transformed['resources']['count'] = $to_transform['resource_type_resources'];
        }

        if (array_key_exists('resources', $this->related) === true) {
            foreach ($this->related['resources'] as $resource) {
                $this->transformed['resources']['collection'][] = (new ResourceTransformer($resource))->asArray();
            }
        }
    }
}
