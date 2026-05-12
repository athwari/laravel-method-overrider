<?php

namespace Athwari\MethodOverrider\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Athwari\MethodOverrider\MethodOverriderServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MethodOverriderServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}