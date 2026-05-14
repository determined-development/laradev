<?php

use Tests\Support\GeneratesFiles;

uses(GeneratesFiles::class);

mutates(\Determined\LaraDev\Console\FacadeMakeCommand::class);

beforeEach(function () {
    $this->files = [
        'app/Support/Facades/Audio.php',
        'app/Support/Facades/Audio/Music.php',
        'app/Support/Facades/Audio/PodcastSelection.php',
        'app/Support/Facades/Bar.php',
        'app/Support/Facades/Bing/Bang.php',
        'app/Support/Facades/Bing/Buzz.php',
        'app/Support/Facades/Customers.php',
        'app/Support/Facades/Commerce/Customers.php',
        'app/Support/Facades/Commerce/Invoices.php',
        'app/Support/Facades/Commerce/Orders.php',
        'app/Support/Facades/Foo.php',
        'app/Support/Facades/Music.php',
        'app/Support/Facades/Orders.php',
        'app/Support/Facades/Podcasts.php',
        'app/Support/Facades/Strings.php',
        'stubs/facade.stub',
        'stubs/facade.targeted.stub',
    ];
});

it('generates a facade file', function () {
    $this->artisan('make:facade', ['name' => 'Podcasts'])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Support\Facades;',
        'use Illuminate\Support\Facades\Facade;',
        'class Podcasts extends Facade',
        "return 'podcasts';",
    ], 'app/Support/Facades/Podcasts.php');
});

it('generates a facade with alias accessor', function () {
    $this->artisan('make:facade', ['name' => 'Commerce/Customers', '--accessor' => 'commerce.customers'])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Support\Facades\Commerce;',
        'use Illuminate\Support\Facades\Facade;',
        'class Customers extends Facade',
        "return 'commerce.customers';",
    ], 'app/Support/Facades/Commerce/Customers.php');
});

it('generates a facade with class accessor', function () {
    $this->artisan('make:facade', ['name' => 'Podcasts', '--accessor' => 'App\\Services\\PodcastSelectionService'])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Support\Facades;',
        'use App\Services\PodcastSelectionService;',
        'use Illuminate\Support\Facades\Facade;',
        '@mixin PodcastSelectionService',
        'class Podcasts extends Facade',
        'return PodcastSelectionService::class;',
    ], 'app/Support/Facades/Podcasts.php');
});

it('generates a facade with target', function () {
    $this->artisan('make:facade', [
        'name' => 'Strings',
        '--target' => 'Illuminate\Support\Stringable',
    ])->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Support\Facades;',
        'use Illuminate\Support\Stringable;',
        'use Illuminate\Support\Facades\Facade;',
        '@mixin Stringable',
        'class Strings extends Facade',
        'return Stringable::class;',
    ], 'app/Support/Facades/Strings.php');
});

it('generates a facade with accessor and target', function () {
    $this->artisan('make:facade', [
        'name' => 'Customers',
        '--accessor' => 'commerce.customers',
        '--target' => 'App\\Services\\Commerce\\CustomerService',
    ])->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Support\Facades;',
        'use App\Services\Commerce\CustomerService;',
        'use Illuminate\Support\Facades\Facade;',
        '@mixin CustomerService',
        'class Customers extends Facade',
        "return 'commerce.customers';",
    ], 'app/Support/Facades/Customers.php');
});

it('detects service for facade', function (string $name, string $target, string $alias, string $namespace) {
    $this->artisan('make:facade', ['name' => $name])
        ->assertSuccessful();

    $this->assertFileContains([
        sprintf('namespace %s;', $namespace),
        sprintf('use %s;', $target),
        'use Illuminate\Support\Facades\Facade;',
        sprintf('@mixin %s', $alias),
        sprintf('class %s extends Facade', str($name)->afterLast('/')),
        sprintf('return %s::class;', $alias),
    ], sprintf('app/Support/Facades/%s.php', $name));
})->with([
    'Services\AudioService' => ['Audio', 'App\Services\AudioService', 'AudioService', 'App\Support\Facades'],
    'Services\Music' => ['Music', 'App\Services\Music as MusicService', 'MusicService', 'App\Support\Facades'],
    'Services\Commerce\OrdersService' => ['Commerce/Orders', 'App\Services\Commerce\OrdersService', 'OrdersService', 'App\Support\Facades\Commerce'],
    'Services\Commerce\Invoices ' => ['Commerce/Invoices', 'App\Services\Commerce\Invoices as InvoicesService', 'InvoicesService', 'App\Support\Facades\Commerce'],
    'Services\PodcastSelection ' => ['Audio/PodcastSelection', 'App\Services\PodcastSelectionService', 'PodcastSelectionService', 'App\Support\Facades\Audio'],
    'Services\Music ' => ['Audio/Music', 'App\Services\Music as MusicService', 'MusicService', 'App\Support\Facades\Audio'],
    'FooService ' => ['Foo', 'App\FooService', 'FooService', 'App\Support\Facades'],
    'Bar ' => ['Bar', 'App\Bar as BarService', 'BarService', 'App\Support\Facades'],
    'Bing\BangService ' => ['Bing/Bang', 'App\Bing\BangService', 'BangService', 'App\Support\Facades\Bing'],
    'Bing\Buzz ' => ['Bing/Buzz', 'App\Bing\Buzz as BuzzService', 'BuzzService', 'App\Support\Facades\Bing'],
]);

it('does not overwrite existing file', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Support/Facades/Podcasts.php'),
        <<<EOF
        <?php

        // test file
        EOF
    );

    $this->artisan('make:facade', ['name' => 'Podcasts'])
        ->assertSuccessful();

    $this->assertFileContains([
        '// test file',
    ], 'app/Support/Facades/Podcasts.php');

    $this->assertFileNotContains([
        'namespace App\Support\Facades;',
        'use Illuminate\Support\Facades\Facade;',
        'class Podcasts extends Facade',
        "return 'podcasts';",
    ], 'app/Support/Facades/Podcasts.php');
});

it('overwrites existing file when forced', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Support/Facades/Podcasts.php'),
        <<<EOF
        <?php

        // test file
        EOF
    );

    $this->artisan('make:facade', ['name' => 'Podcasts', '--force' => true])
        ->assertSuccessful();

    $this->assertFileNotContains([
        '// test file',
    ], 'app/Support/Facades/Podcasts.php');

    $this->assertFileContains([
        'namespace App\Support\Facades;',
        'use Illuminate\Support\Facades\Facade;',
        'class Podcasts extends Facade',
        "return 'podcasts';",
    ], 'app/Support/Facades/Podcasts.php');
});

it('resolves stub override for non-targeted stub', function () {
    $this->app['files']->ensureDirectoryExists($this->app->basePath('stubs'));

    $this->app['files']->put(
        $this->app->basePath('stubs/facade.stub'),
        <<<EOF
        <?php

        namespace {{ namespace }};

        use Illuminate\Support\Facades\Facade;

        class {{ class }} extends Facade
        {
            // Overwritten stub
            protected static function getFacadeAccessor(): string
            {
                return {{ accessor }};
            }
        }

        EOF
    );

    $this->artisan('make:facade', ['name' => 'Podcasts'])
        ->assertSuccessful();

    $this->assertFileContains([
        '// Overwritten stub',
    ], 'app/Support/Facades/Podcasts.php');
});

it('resolves stub override for targeted stub', function (array $params, string $file) {
    $this->app['files']->ensureDirectoryExists($this->app->basePath('stubs'));

    $this->app['files']->put(
        $this->app->basePath('stubs/facade.targeted.stub'),
        <<<EOF
        <?php

        namespace {{ namespace }};

        use {{ target }};

        use Illuminate\Support\Facades\Facade;
        /**
         * @mixin {{ alias }}
         */
        class {{ class }} extends Facade
        {
            // Overwritten stub
            protected static function getFacadeAccessor(): string
            {
                return {{ accessor }};
            }
        }

        EOF
    );

    $this->artisan('make:facade', $params)
        ->assertSuccessful();

    $this->assertFileContains([
        '// Overwritten stub',
    ], $file);
})->with([
    'detected service' => [['name' => 'Audio'], 'app/Support/Facades/Audio.php'],
    'class accessor' => [['name' => 'Orders', '--accessor' => 'App\Services\Commerce\OrdersService'], 'app/Support/Facades/Orders.php'],
    'target class' => [['name' => 'Podcasts', '--target' => 'App\Services\PodcastSelectionService'], 'app/Support/Facades/Podcasts.php'],
]);
