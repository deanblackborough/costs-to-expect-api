<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Laravel\Passport\Events\AccessTokenCreated' => [
            \App\Listeners\RevokeTokens::class,
            \App\Listeners\PruneTokens::class,
        ],
        \App\Events\RequestError::class => [
            \App\Listeners\CaptureAndSendRequestError::class,
        ],
        \App\Events\InternalError::class => [
            \App\Listeners\CaptureAndSendInternalError::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
