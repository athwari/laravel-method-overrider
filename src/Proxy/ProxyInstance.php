<?php

namespace Athwari\MethodOverrider\Proxy;

class ProxyInstance
{
    public function __construct(
        protected object $target,
        protected array $methodMap = []
    ) {}

    public function __call(string $method, array $args)
    {
        // no override → normal execution
        if (! isset($this->methodMap[$method])) {
            return $this->target->$method(...$args);
        }

        $original = fn (...$args) =>
            $this->target->$method(...$args);

        return ($this->methodMap[$method])($original, ...$args);
    }
}