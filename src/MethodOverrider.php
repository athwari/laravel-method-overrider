<?php

namespace Athwari\MethodOverrider;

use Athwari\MethodOverrider\Exceptions\ClassNotFoundException;
use Athwari\MethodOverrider\Exceptions\FinalMethodCannotBeOverriddenException;
use Athwari\MethodOverrider\Exceptions\InvalidImplementationException;
use Athwari\MethodOverrider\Exceptions\MethodNotFoundException;
use Athwari\MethodOverrider\Proxy\ProxyFactory;
use ReflectionClass;

class MethodOverrider
{
    public function __construct(
        protected ProxyFactory $factory
    ) {}

    public function override(
        string $class,
        string|array $methods,
        callable|array $implementations
    ): object {
        $methods = (array) $methods;
        $implementations = (array) $implementations;

        if (! class_exists($class)) {
            throw new ClassNotFoundException($class);
        }

        if (count($methods) !== count($implementations)) {
            throw new InvalidImplementationException(
                'Methods count must match implementations count.'
            );
        }

        $reflection = new ReflectionClass($class);

        foreach ($methods as $index => $method) {
            if (! $reflection->hasMethod($method)) {
                throw new MethodNotFoundException($method);
            }

            $methodReflection = $reflection->getMethod($method);

            if ($methodReflection->isFinal()) {
                throw new FinalMethodCannotBeOverriddenException($method);
            }
        }

        return $this->factory->make(
            $class,
            $methods,
            $implementations
        );
    }
}
