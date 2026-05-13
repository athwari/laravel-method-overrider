<?php

namespace Athwari\MethodOverrider\Proxy;

class ProxyInstance
{
    protected static array $staticMethodMap = [];
    protected static ?string $staticTargetClass = null;

    public function __construct(
        protected object $target,
        protected array $methodMap = []
    ) {
        self::$staticTargetClass = get_class($target);
        self::$staticMethodMap = [];

        foreach ($methodMap as $name => $implementation) {
            if (method_exists($target, $name)) {
                $reflection = new \ReflectionMethod($target, $name);

                if ($reflection->isStatic()) {
                    self::$staticMethodMap[$name] = $implementation;
                }
            }
        }
    }

    public function __call(string $method, array $args)
    {
        if (! isset($this->methodMap[$method])) {
            return $this->target->$method(...$args);
        }

        $original = function (&...$args) use ($method) {
            return $this->target->$method(...$args);
        };

        return ($this->methodMap[$method])($original, ...$args);
    }

    public static function __callStatic(string $method, array $args)
    {
        if (! isset(self::$staticMethodMap[$method]) || self::$staticTargetClass === null) {
            return forward_static_call_array([
                self::$staticTargetClass,
                $method,
            ], $args);
        }

        $original = function (&...$args) use ($method) {
            return forward_static_call_array([
                self::$staticTargetClass,
                $method,
            ], $args);
        };

        return (self::$staticMethodMap[$method])($original, ...$args);
    }
}
