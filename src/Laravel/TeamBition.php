<?php

namespace Stackboom\Teambition\Laravel;

use Stackboom\Teambition\TeamBitionOpenAPIClient;

/**
 * Class TeamBition
 * @package App\Lib\TeamBition
 * @mixin TeamBitionOpenAPIClient
 */
class TeamBition extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'teambition';
    }
}
