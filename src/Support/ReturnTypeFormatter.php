<?php

namespace Athwari\MethodOverrider\Support;

use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

class ReturnTypeFormatter
{
    public function format(?ReflectionType $type): string
    {
        if (! $type) {
            return '';
        }

        $formatted = '';

        if ($type instanceof ReflectionNamedType) {
            $name = $type->getName();

            $formatted = $type->allowsNull() && $name !== 'mixed'
                ? '?'.$name
                : $name;
        }

        if ($type instanceof ReflectionUnionType) {
            $formatted = implode(
                '|',
                array_map(
                    fn ($t) => $t->getName(),
                    $type->getTypes()
                )
            );
        }

        return ': '.$formatted;
    }
}
