<?php

namespace Athwari\MethodOverrider\Proxy;

use Athwari\MethodOverrider\Proxy\ProxyClassGenerator;

class ProxyFactory
{
    public function __construct(
        protected ProxyClassGenerator $generator
    ) {}

    public function make(
        string $class,
        array $methods,
        array $implementations
    ): object {
        if (empty($methods)) {
            return app($class);
        }

        $proxyClassName = 'MethodOverriderProxy_'.md5(
            $class.':'.implode(',', $methods).':'.uniqid('', true)
        );

        $classCode = $this->generator->generate(
            $proxyClassName,
            $class,
            $methods,
            $implementations
        );

        if (! class_exists($proxyClassName, false)) {
            eval($classCode);
        }

        return new $proxyClassName($implementations);
    }
}
