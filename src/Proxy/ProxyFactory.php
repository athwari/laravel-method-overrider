<?php

namespace Athwari\MethodOverrider\Proxy;

class ProxyFactory
{
    public function make(
        string $class,
        array $methods,
        array $implementations
    ): object {
        $map = array_combine($methods, $implementations);

        return new ProxyInstance(
            app($class),
            $map
        );
    }
}