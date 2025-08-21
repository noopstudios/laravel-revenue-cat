<?php

namespace NoopStudios\LaravelRevenueCat\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NoopStudios\LaravelRevenueCat\LaravelRevenueCat
 */
class LaravelRevenueCat extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \NoopStudios\LaravelRevenueCat\LaravelRevenueCat::class;
    }
}
