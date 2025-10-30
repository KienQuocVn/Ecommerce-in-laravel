<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Payments\Gateways\PaypalGateway;
use App\Payments\Gateways\MomoGateway;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Stripe
        $this->app->singleton(StripeClient::class, function () {
            return new StripeClient(config('services.stripe.secret'));
        });

        // PayPal
        $this->app->bind(PaypalGateway::class, fn () => new PaypalGateway());
        
        $this->app->bind(MomoGateway::class, fn() => new MomoGateway());

    }

    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}