<?php

namespace Athwari\MethodOverrider\Tests\Unit;

use Athwari\MethodOverrider\Support\ReturnTypeFormatter;
use ReflectionMethod;

class ReturnTypeFormatterTestClass
{
    public function simple(): string
    {
        return '';
    }

    public function nullable(): ?string
    {
        return null;
    }

    public function union(): string|int
    {
        return '';
    }
}

it('formats simple return type', function () {

    $formatter = app(ReturnTypeFormatter::class);

    $method = new ReflectionMethod(
        ReturnTypeFormatterTestClass::class,
        'simple'
    );

    expect(
        $formatter->format($method->getReturnType())
    )->toBe(': string');
});

it('formats nullable return type', function () {

    $formatter = app(ReturnTypeFormatter::class);

    $method = new ReflectionMethod(
        ReturnTypeFormatterTestClass::class,
        'nullable'
    );

    expect(
        $formatter->format($method->getReturnType())
    )->toBe(': ?string');
});

it('formats union return type', function () {

    $formatter = app(ReturnTypeFormatter::class);

    $method = new ReflectionMethod(
        ReturnTypeFormatterTestClass::class,
        'union'
    );

    expect(
        $formatter->format($method->getReturnType())
    )->toBe(': string|int');
});
