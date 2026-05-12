<?php

namespace Athwari\MethodOverrider;

use Athwari\MethodOverrider\Support\ParameterFormatter;
use ReflectionClass;

class ProxyClassGenerator
{
    public function __construct(
        protected MethodSignatureBuilder $signatureBuilder,
        protected ParameterFormatter $parameterFormatter,
    ) {}

    public function generate(
        string $class,
        array $methods,
        array $implementations,
    ): object {

        $reflection = new ReflectionClass($class);

        $definitions = [];

        foreach ($methods as $index => $methodName) {

            $method = $reflection->getMethod($methodName);

            if ($method->isFinal()) {
                continue;
            }

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

        $anonymousClass = <<<PHP
return new class(\$implementations) extends \\{$class}
{
    protected array \$implementations;
    protected static array \$staticImplementations = [];

    public function __construct(array \$implementations)
    {
        \$this->implementations = \$implementations;
        self::\$staticImplementations = \$implementations;
    }

    {$methodsCode}
};
PHP;

        return eval($anonymousClass);
    }
}
