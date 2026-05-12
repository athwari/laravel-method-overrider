<?php

namespace Athwari\MethodOverrider\Support;

use ReflectionNamedType;
use ReflectionParameter;
use ReflectionType;
use ReflectionUnionType;

class ParameterFormatter
{
    public function format(ReflectionParameter $parameter): string
    {
        $code = '';

        if ($parameter->hasType()) {
            $code .= $this->formatType($parameter->getType()).' ';
        }

        if ($parameter->isPassedByReference()) {
            $code .= '&';
        }

        if ($parameter->isVariadic()) {
            $code .= '...';
        }

        $code .= '$'.$parameter->getName();

        if (
            $parameter->isDefaultValueAvailable()
            && ! $parameter->isVariadic()
        ) {
            $code .= ' = '.var_export(
                $parameter->getDefaultValue(),
                true
            );
        }

        return $code;
    }

    private function formatType(ReflectionType $type): string
    {
        if ($type instanceof ReflectionNamedType) {
            $name = $type->getName();

            if ($type->allowsNull() && $name !== 'mixed') {
                return '?'.$name;
            }

            return $name;
        }

        if ($type instanceof ReflectionUnionType) {
            return implode('|', array_map(
                fn ($t) => $t->getName(),
                $type->getTypes()
            ));
        }

        return '';
    }
}
