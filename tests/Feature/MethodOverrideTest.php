<?php

namespace Athwari\MethodOverrider\Tests\Feature;

use Athwari\MethodOverrider\Exceptions\ClassNotFoundException;
use Athwari\MethodOverrider\Exceptions\FinalMethodCannotBeOverriddenException;
use Athwari\MethodOverrider\Exceptions\InvalidImplementationException;
use Athwari\MethodOverrider\Exceptions\MethodNotFoundException;
use Athwari\MethodOverrider\Facades\MethodOverrider;

class TestService
{
    public function greet(string $name): string
    {
        return "Hello {$name}";
    }

    public function nullable(?string $name): ?string
    {
        return $name;
    }

    public function union(string|int $value): string|int
    {
        return $value;
    }

    public function variadic(string ...$items): array
    {
        return $items;
    }

    public function byReference(string &$value): string
    {
        $value = strtoupper($value);

        return $value;
    }

    final public function finalMethod(): string
    {
        return 'final';
    }

    protected function protectedMethod(): string
    {
        return 'protected';
    }

    public static function staticMethod(): string
    {
        return 'static';
    }
}

it('overrides a method', function () {

    $service = MethodOverrider::override(
        TestService::class,
        'greet',
        function ($original, $name) {
            return strtoupper($original($name));
        }
    );

    expect($service->greet('taylor'))
        ->toBe('HELLO TAYLOR');
});

it('supports nullable parameters', function () {

    $service = MethodOverrider::override(
        TestService::class,
        'nullable',
        fn ($original, $name) => $original($name)
    );

    expect($service->nullable(null))
        ->toBeNull();
});

it('supports union types', function () {

    $service = MethodOverrider::override(
        TestService::class,
        'union',
        fn ($original, $value) => $original($value)
    );

    expect($service->union('hello'))
        ->toBe('hello');

    expect($service->union(123))
        ->toBe(123);
});

it('supports variadic parameters', function () {

    $service = MethodOverrider::override(
        TestService::class,
        'variadic',
        fn ($original, ...$items) => $original(...$items)
    );

    expect($service->variadic('a', 'b', 'c'))
        ->toBe(['a', 'b', 'c']);
});

it('supports reference parameters', function () {

    $service = MethodOverrider::override(
        TestService::class,
        'byReference',
        function ($original, &$value) {
            return $original($value);
        }
    );

    $value = 'hello';

    $service->byReference($value);

    expect($value)->toBe('HELLO');
});

it('supports static methods', function () {

    $service = MethodOverrider::override(
        TestService::class,
        'staticMethod',
        fn ($original) => strtoupper($original())
    );

    expect($service::staticMethod())
        ->toBe('STATIC');
});

it('throws exception for invalid class', function () {

    MethodOverrider::override(
        'FakeClass',
        'test',
        fn () => null
    );

})->throws(ClassNotFoundException::class);

it('throws exception for invalid method', function () {

    MethodOverrider::override(
        TestService::class,
        'missingMethod',
        fn () => null
    );

})->throws(MethodNotFoundException::class);

it('throws exception for invalid implementation count', function () {

    MethodOverrider::override(
        TestService::class,
        ['greet', 'nullable'],
        [
            fn () => null,
        ]
    );

})->throws(InvalidImplementationException::class);

it('throws exception for final methods', function () {
    MethodOverrider::override(
        TestService::class,
        'finalMethod',
        fn () => 'changed'
    );

})->throws(FinalMethodCannotBeOverriddenException::class);
