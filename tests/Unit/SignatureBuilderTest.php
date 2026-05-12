<?php

namespace Athwari\MethodOverrider\Tests\Unit;

use ReflectionMethod;
use Athwari\MethodOverrider\Support\ParameterFormatter;
use Athwari\MethodOverrider\Support\ReturnTypeFormatter;

class SignatureBuilder
{
    public function __construct(
        protected ParameterFormatter $parameterFormatter,
        protected ReturnTypeFormatter $returnTypeFormatter,
    ) {}

    public function build(ReflectionMethod $method): string
    {
        $parameters = array_map(
            fn ($parameter) => $this->parameterFormatter->format($parameter),
            $method->getParameters()
        );

        return sprintf(
            '%s(%s)%s',
            $method->getName(),
            implode(', ', $parameters),
            $this->returnTypeFormatter->format($method->getReturnType()),
        );
    }
}
