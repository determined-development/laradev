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

You can use this utility to make facades for services in your application.

```bash
php artisan make:facade Foo

  INFO  Facade [app/Support/Facades/Foo.php] created successfully
```

The facade accessor will be guessed from the name of the facade, or can be set explicitly with the `--accessor` or `--target` options. If the accessor, target, or detected service is a class, then a mixin will be added for PHPStan/IDE hinting.

```bash
php artisan make:facade Foo --accessor=foo.service --target=App\Services\FooBarService
```
Will generate

```php
<?php

namespace App\Support\Facades;

use App\Services\FooBarService;
use Illuminate\Support\Facades\Facade;

/**
 * @mixin FooBarService
 */
class Foo extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'foo.service';
    }
}
```

### Publishing stubs

```bash
php artisan vendor:publish --tag=laradev:stubs
```
