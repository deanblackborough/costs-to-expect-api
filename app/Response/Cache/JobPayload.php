<?php

declare(strict_types=1);

namespace App\Response\Cache;

/**
 * Generate the data we need the ClearResourceTypeIdCache job. The job
 * is responsible for fetching and clearing the keys, we pass in the minimum
 * necessary.
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class JobPayload
{
    private array $payload;

    public function __construct()
    {
        $this->payload = [
            'permitted_user' => false,
            'route_parameters' => [],
            'key' => null,
            'user_id' => null,
        ];
    }

    public function setUserId(int $id): self
    {
        $this->payload['user_id'] = $id;

        return $this;
    }

    public function setPermittedUser(bool $permitted = false): self
    {
        $this->payload['permitted_user'] = $permitted;

        return $this;
    }

    public function setRouteParameters(array $parameters): self
    {
        $this->payload['route_parameters'] = $parameters;

        return $this;
    }

    public function setGroupKey(string $key): self
    {
        $this->payload['group_key'] = $key;

        return $this;
    }

    public function payload(): array
    {
        return $this->payload;
    }
}
