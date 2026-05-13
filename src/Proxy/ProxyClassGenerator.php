<?php

namespace Athwari\MethodOverrider\Proxy;

use Athwari\MethodOverrider\Support\MethodSignatureBuilder;
use Athwari\MethodOverrider\Support\ParameterFormatter;
use ReflectionClass;

class ProxyClassGenerator
{
    public function __construct(
        protected MethodSignatureBuilder $signatureBuilder,
        protected ParameterFormatter $parameterFormatter,
    ) {}

    public function generate(
        string $proxyClassName,
        string $class,
        array $methods,
        array $implementations,
    ): string {
        $reflection = new ReflectionClass($class);
        $definitions = [];
        $hasStaticMethods = false;

        foreach ($methods as $index => $methodName) {
            $method = $reflection->getMethod($methodName);

            $signature = $this->signatureBuilder->build($method);

            $arguments = collect($method->getParameters())
                ->map(fn ($parameter) => $parameter->isVariadic()
                    ? '...$'.$parameter->getName()
                    : '$'.$parameter->getName()
                )
                ->implode(', ');

            $closureParameters = collect($method->getParameters())
                ->map(fn ($parameter) => $this->parameterFormatter->format($parameter))
                ->implode(', ');

            $parentCallArguments = collect($method->getParameters())
                ->map(fn ($parameter) => $parameter->isVariadic()
                    ? '...$'.$parameter->getName()
                    : '$'.$parameter->getName()
                )
                ->implode(', ');

            $implementationsAccess = $method->isStatic()
                ? 'self::$staticImplementations'
                : '$this->implementations';

            if ($method->isStatic()) {
                $hasStaticMethods = true;
            }

            $argumentList = $arguments ? ', '.$arguments : '';

            $definitions[] = <<<PHP
{$signature}
{
    \$original = function({$closureParameters}) {
        return parent::{$methodName}({$parentCallArguments});
    };

    return ({$implementationsAccess}[{$index}])(
        \$original{$argumentList}
    );
}
PHP;
        }

        $methodsCode = implode("\n\n", $definitions);
        $parentClass = '\\'.$class;
        $staticProperty = $hasStaticMethods ? 'protected static array $staticImplementations = [];' : '';
        $staticSetter = $hasStaticMethods ? 'self::$staticImplementations = $implementations;' : '';

        return <<<PHP
declare(strict_types=1);

class {$proxyClassName} extends {$parentClass}
{
    protected array \$implementations;
    {$staticProperty}

    public function __construct(array \$implementations)
    {
        \$this->implementations = \$implementations;
        {$staticSetter}
    }

    {$methodsCode}
}
PHP;
    }
}
