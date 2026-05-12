# Laravel Method Overrider

[![Latest Version on Packagist](https://img.shields.io/packagist/v/athwari/laravel-method-overrider.svg?style=flat-square)](https://packagist.org/packages/athwari/laravel-method-overrider)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/athwari/laravel-method-overrider/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/athwari/laravel-method-overrider/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/athwari/laravel-method-overrider/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/athwari/laravel-method-overrider/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/athwari/laravel-method-overrider.svg?style=flat-square)](https://packagist.org/packages/athwari/laravel-method-overrider)

Runtime method interception and overriding for Laravel applications.

This package allows you to override instance and static methods on a class at runtime by generating a proxy class that delegates to an implementation closure.

## Installation

Install the package with Composer:

```bash
composer require athwari/laravel-method-overrider
```

Laravel package auto-discovery is supported, so the service provider and facade are registered automatically.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=method-overrider-config
```

The published config file is located at `config/method-overrider.php`.

### Default configuration

```php
return [
    'ignore_final_methods' => true,
];
```

- `ignore_final_methods`: when `true`, final methods are skipped during override generation instead of throwing an exception.

## Usage

Use the `MethodOverrider` facade to override methods on a target class.

### Override a method

```php
use Athwari\MethodOverrider\Facades\MethodOverrider;

class TestService
{
    public function greet(string $name): string
    {
        return "Hello {$name}";
    }
}

$service = MethodOverrider::override(
    TestService::class,
    'greet',
    function ($original, $name) {
        return strtoupper($original($name));
    }
);

echo $service->greet('Taylor'); // HELLO TAYLOR
```

### Override multiple methods

```php
$service = MethodOverrider::override(
    TestService::class,
    ['greet', 'nullable'],
    [
        function ($original, $name) {
            return strtoupper($original($name));
        },
        function ($original, $name) {
            return $original($name);
        },
    ]
);
```

### Supported method signatures

The package supports:

- nullable and union parameter types
- variadic parameters
- reference parameters
- static methods
- return types

Final methods are skipped when `ignore_final_methods` is enabled.

## Exceptions

The package throws exceptions for invalid usage:

- `Athwari\MethodOverrider\Exceptions\ClassNotFoundException`
- `Athwari\MethodOverrider\Exceptions\MethodNotFoundException`
- `Athwari\MethodOverrider\Exceptions\InvalidImplementationException`

## Testing

Run the test suite with Pest:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [athwari](https://github.com/athwari)
- [All Contributors](../../contributors)

## License

The package is open-source software licensed under the MIT License. Please see [License File](LICENSE.md) for more information.