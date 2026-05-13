<?php

namespace Athwari\MethodOverrider\Support;

use Athwari\MethodOverrider\Support\ParameterFormatter;
use Athwari\MethodOverrider\Support\ReturnTypeFormatter;
use ReflectionMethod;

class MethodSignatureBuilder
{
    public function __construct(
        protected ParameterFormatter $parameterFormatter,
        protected ReturnTypeFormatter $returnTypeFormatter,
    ) {}

    public function build(ReflectionMethod $method): string
    {
        $name = $method->getName();

        $visibility = $method->isProtected()
            ? 'protected'
            : 'public';

        $static = $method->isStatic()
            ? ' static'
            : '';

        $reference = $method->returnsReference()
            ? '&'
            : '';

        $parameters = collect($method->getParameters())
            ->map(fn ($parameter) => $this->parameterFormatter->format($parameter))
            ->implode(', ');

        $returnType = $this->returnTypeFormatter
            ->format($method->getReturnType());

        return sprintf(
            '%s%s function %s%s(%s)%s',
            $visibility,
            $static,
            $reference,
            $name,
            $parameters,
            $returnType,
        );
    }
}
