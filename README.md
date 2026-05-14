# LaraDev

<p align="center">
<a href="https://github.com/determined-development/laradev/actions"><img src="https://github.com/determined-development/laradev/actions/workflows/tests.yml/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/determined-development/laradev"><img src="https://img.shields.io/packagist/dt/determined-development/laradev" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/determined-development/laradev"><img src="https://img.shields.io/packagist/v/determined-development/laradev" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/determined-development/laradev"><img src="https://img.shields.io/packagist/l/determined-development/laradev" alt="License"></a>
</p>

A collection of Laravel development helpers and utilities.

## Installation

```bash
composer require --dev determined-development/laradev
```

## Usage

### `make:facade`

You can use this utility to make facades for services in your application. The facade accessor will be guessed from the name of the facade, or can be set explicitly with the `--accessor` or `--target` options. If the accessor, target, or detected service is a class, then a mixin will be added for PHPStan/IDE hinting.

```bash
php artisan make:facade Foo

  INFO  Facade [app/Support/Facades/Foo.php] created successfully
  
php artisan make:facade Foo/Bar --accessor='App\Services\FooBarService'

  INFO  Facade [app/Support/Facades/Foo/Bar.php] created successfully
  
php artisan make:facade Fizz/Buzz --accessor='fizzbuzz' --target='App\Services\FizzBuzzService'

  INFO  Facade [app/Support/Facades/Fizz/Buzz.php] created successfully
```

### `make:service`

You can use this utility to make services in your application. You can pass `--facade` to also generate a matching facade. If you don't set the value, it will name the Facade automatically based on the service name.

```bash
php artisan make:service Foo

  INFO  Service [app/Services/Foo.php] created successfully

php artisan make:service FooService --facade

  INFO  Service [app/Services/FooService.php] created successfully
  INFO  Facade [app/Support/Facades/Foo.php] created successfully

php artisan make:service Foo/Bar --facade

  INFO  Service [app/Services/Foo/Bar.php] created successfully
  INFO  Facade [app/Support/Facades/Foo/Bar.php] created successfully
  
php artisan make:service Fizz/Buzz --facade=Bing

  INFO  Service [app/Services/Fizz/Buzz.php] created successfully
  INFO  Facade [app/Support/Facades/Bing.php] created successfully
```


### Publishing stubs

Stubs can be published with any of the following commands:

```bash
php artisan vendor:publish --tag=laradev
php artisan vendor:publish --tag=stubs
php artisan vendor:publish --provider='Determined\LaraDev\LaraDevServiceProvider' --tag=stubs
```
