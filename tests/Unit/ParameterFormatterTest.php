<?php

namespace Athwari\MethodOverrider\Tests\Unit;

use Athwari\MethodOverrider\Support\ParameterFormatter;
use ReflectionMethod;

class ParameterFormatterTestClass
{
    public function simple(string $name): void {}

    public function nullable(?string $name): void {}

    public function union(string|int $value): void {}

    public function variadic(string ...$items): void {}

    public function reference(string &$value): void {}

    public function defaultValue(string $name = 'test'): void {}
}

it('formats simple parameter', function () {

    $formatter = app(ParameterFormatter::class);

    $method = new ReflectionMethod(
        ParameterFormatterTestClass::class,
        'simple'
    );

    $parameter = $method->getParameters()[0];

    expect($formatter->format($parameter))
        ->toBe('string $name');
});

it('formats nullable parameter', function () {

    $formatter = app(ParameterFormatter::class);

    $method = new ReflectionMethod(
        ParameterFormatterTestClass::class,
        'nullable'
    );

    $parameter = $method->getParameters()[0];

    expect($formatter->format($parameter))
        ->toBe('?string $name');
});

it('formats union parameter', function () {

    $formatter = app(ParameterFormatter::class);

    $method = new ReflectionMethod(
        ParameterFormatterTestClass::class,
        'union'
    );

    $parameter = $method->getParameters()[0];

    expect($formatter->format($parameter))
        ->toBe('string|int $value');
});

it('formats variadic parameter', function () {

    $formatter = app(ParameterFormatter::class);

    $method = new ReflectionMethod(
        ParameterFormatterTestClass::class,
        'variadic'
    );

    $parameter = $method->getParameters()[0];

    expect($formatter->format($parameter))
        ->toBe('string ...$items');
});

it('formats reference parameter', function () {

    $formatter = app(ParameterFormatter::class);

    $method = new ReflectionMethod(
        ParameterFormatterTestClass::class,
        'reference'
    );

    $parameter = $method->getParameters()[0];

    expect($formatter->format($parameter))
        ->toBe('string &$value');
});

it('formats default values', function () {

    $formatter = app(ParameterFormatter::class);

    $method = new ReflectionMethod(
        ParameterFormatterTestClass::class,
        'defaultValue'
    );

    $parameter = $method->getParameters()[0];

    expect($formatter->format($parameter))
        ->toBe('string $name = \'test\'');
});
