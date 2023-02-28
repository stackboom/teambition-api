<?php

namespace Stackboom\Teambition\Laravel;

use Illuminate\Support\ServiceProvider;
use Stackboom\Teambition\TeamBitionOpenAPIClient;

class TeamBitionServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->bind( TeamBitionOpenAPIClient::class, function () {
            return new TeamBitionOpenAPIClient(
                env('TEAMBITION_APP_ID'),
                env('TEAMBITION_APP_SECRET')
            );
        });
        $this->app->alias(TeamBitionOpenAPIClient::class, 'teambition');
    }
}
