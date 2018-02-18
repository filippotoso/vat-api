<?php

namespace FilippoToso\VatApi;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class VatApiServiceProvider extends ServiceProvider
{

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {

        $this->app->singleton('vat', function () {
            return new VatApi();
        });

    }

}
