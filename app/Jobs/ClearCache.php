<?php

namespace App\Jobs;

use App\Cache\Control;
use App\Cache\Job;
use App\Cache\KeyGroup;
use App\Cache\Trash;
use App\Models\ResourceAccess;
use App\Models\ResourceType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Clear the requested cache keys
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2021
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class ClearCache implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = new Job($this->payload);

        $cache_control = new Control(
            $payload->permittedUser(),
            $payload->userId()
        );

        $cache_key_group = new KeyGroup($payload->routeParameters());
        $cache_keys = $cache_key_group->keys($payload->groupKey());

        if (array_key_exists('resource_type_id', $payload->routeParameters())) {
            $permitted_users = (new ResourceAccess())->permittedResourceTypeUsers(
                $payload->routeParameters()['resource_type_id'],
                $payload->userId()
            );

            $public_resource_types = (new ResourceType())->publicResourceTypes();

            $trash = new Trash(
                $cache_control,
                $cache_keys,
                $payload->routeParameters()['resource_type_id'],
                $public_resource_types,
                $permitted_users
            );

            $trash->all();
        } else {
            $cache_control->clearMatchingCacheKeys($cache_keys);
        }
    }
}
