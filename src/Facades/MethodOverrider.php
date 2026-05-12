<?php

namespace Athwari\MethodOverrider\Facades;

use Illuminate\Support\Facades\Facade;

class MethodOverrider extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Athwari\MethodOverrider\MethodOverrider::class;
    }
}
