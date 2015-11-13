<?php

namespace Qhp\AmazonPayments;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Config;

class AmazonPaymentsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/amazonPayments.php' => config_path('amazonPayments.php'),
        ]);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('AmazonPayments', function() {

            if (! Config::has('amazonPayments')) {
                throw new FileException('Config amazonPayments not exists.');
            }

            return new AmazonPayments(new AmazonPaymentsClient(config('amazonPayments')));
        });
    }
}