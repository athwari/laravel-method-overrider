<?php

namespace Athwari\MethodOverrider;

use Athwari\MethodOverrider\Exceptions\ClassNotFoundException;
use Athwari\MethodOverrider\Exceptions\InvalidImplementationException;
use Athwari\MethodOverrider\Exceptions\MethodNotFoundException;

class MethodOverrider
{
    public function __construct(
        protected ProxyClassGenerator $generator,
    ) {}

    public function override(
        string $class,
        string|array $methods,
        callable|array $implementations,
    ): object {

        $methods = (array) $methods;
        $implementations = (array) $implementations;

        if (! class_exists($class)) {
            throw new ClassNotFoundException($class);
        }

        if (count($methods) !== count($implementations)) {
            throw new InvalidImplementationException;
        }

        foreach ($methods as $method) {
            if (! method_exists($class, $method)) {
                throw new MethodNotFoundException($method);
            }
        }

        return $this->generator->generate(
            $class,
            $methods,
            $implementations,
        );
    }
}
