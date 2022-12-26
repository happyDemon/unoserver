<?php

namespace HappyDemon\UnoServer\Facades;

use HappyDemon\UnoServer\UnoServerFactory;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \HappyDemon\UnoServer\UnoServer connection(?string $serverName)
 * @method static \HappyDemon\UnoServer\UnoServer fromConfig(array $configData)
 */
class UnoServer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return UnoServerFactory::class;
    }
}
