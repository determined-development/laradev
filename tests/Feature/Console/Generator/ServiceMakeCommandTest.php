<?php

use Determined\LaraDev\Console\ServiceMakeCommand;
use Tests\Support\GeneratesFiles;

uses(GeneratesFiles::class);

mutates(ServiceMakeCommand::class);

beforeEach(function () {
    $this->files = [
        'app/Services/Alpha.php',
        'app/Services/BetaService.php',
        'app/Services/GammaService.php',
        'app/Services/EpsilonService.php',
        'app/Support/Facades/Beta.php',
        'app/Support/Facades/Gamma.php',
        'app/Support/Facades/Delta.php',
        'app/Support/Facades/Epsilon.php',
        'app/Support/Facades/SomeClass.php',
        'app/Support/Services/class.php',
        'stubs/service.stub',
        'stubs/service.singleton.stub',
    ];
});

it('generates a service file', function () {
    $this->artisan('make:service', ['name' => 'Alpha'])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Services;',
        'use Illuminate\Container\Attributes\Config;',
        'use Illuminate\Container\Attributes\Give;',
        'class Alpha',
    ], 'app/Services/Alpha.php');

    $this->assertFileNotContains([
        'Singleton',
        '#[Singleton]',
    ], 'app/Services/Alpha.php');
});

it('generates a singleton service file', function () {
    $this->artisan('make:service', ['name' => 'Alpha', '--singleton' => true])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\Services;',
        'use Illuminate\Container\Attributes\Config;',
        'use Illuminate\Container\Attributes\Give;',
        'use Illuminate\Container\Attributes\Singleton;',
        '#[Singleton]',
        'class Alpha',
    ], 'app/Services/Alpha.php');
});

it('generates a facade for the service when requested', function () {
    $this->artisan('make:service', ['name' => 'BetaService', '--facade' => true])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\\Support\\Facades;',
        'use Illuminate\\Support\\Facades\\Facade;',
        'class Beta extends Facade',
    ], 'app/Support/Facades/Beta.php');
});

it('generates a facade with a custom facade name', function () {
    $this->artisan('make:service', ['name' => 'GammaService', '--facade' => 'Gamma'])
        ->assertSuccessful();

    $this->assertFileContains([
        'namespace App\\Support\\Facades;',
        'class Gamma extends Facade',
    ], 'app/Support/Facades/Gamma.php');
});

it('does not overwrite existing file', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Services/Alpha.php'),
        <<<'EOF'
        <?php

        // test file
        EOF
    );

    $this->artisan('make:service', ['name' => 'Alpha'])
        ->assertSuccessful();

    $this->assertFileContains([
        '// test file',
    ], 'app/Services/Alpha.php');

    $this->assertFileNotContains([
        'namespace App\Services;',
        'class Alpha',
    ], 'app/Services/Alpha.php');
});

it('overwrites existing file when forced', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Services/Alpha.php'),
        <<<'EOF'
        <?php

        // test file
        EOF
    );

    $this->artisan('make:service', ['name' => 'Alpha', '--force' => true])
        ->assertSuccessful();

    $this->assertFileNotContains([
        '// test file',
    ], 'app/Services/Alpha.php');

    $this->assertFileContains([
        'namespace App\Services;',
        'class Alpha',
    ], 'app/Services/Alpha.php');
});

it('resolves stub override for default stub', function () {
    $this->app['files']->ensureDirectoryExists($this->app->basePath('stubs'));

    $this->app['files']->put(
        $this->app->basePath('stubs/service.stub'),
        <<<'EOF'
        <?php

        namespace {{ namespace }};

        class {{ class }}
        {
            // Overwritten stub
        }

        EOF
    );

    $this->artisan('make:service', ['name' => 'Alpha'])
        ->assertSuccessful();

    $this->assertFileContains([
        '// Overwritten stub',
    ], 'app/Services/Alpha.php');
});

it('resolves stub override for singleton stub', function () {
    $this->app['files']->ensureDirectoryExists($this->app->basePath('stubs'));

    $this->app['files']->put(
        $this->app->basePath('stubs/service.singleton.stub'),
        <<<'EOF'
        <?php

        namespace {{ namespace }};

        class {{ class }}
        {
            // Overwritten singleton stub
        }

        EOF
    );

    $this->artisan('make:service', ['name' => 'Alpha', '--singleton' => true])
        ->assertSuccessful();

    $this->assertFileContains([
        '// Overwritten singleton stub',
    ], 'app/Services/Alpha.php');
});

it('does not overwrite existing facade when force is not enabled', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Support/Facades/Delta.php'),
        <<<'EOF'
        <?php

        // existing facade file
        EOF
    );

    $this->artisan('make:service', ['name' => 'GammaService', '--facade' => 'Delta'])
        ->assertSuccessful();

    $this->assertFileContains([
        '// existing facade file',
    ], 'app/Support/Facades/Delta.php');
});

it('overwrites existing facade when force is enabled', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Support/Facades/Delta.php'),
        <<<'EOF'
        <?php

        // existing facade file
        EOF
    );

    $this->artisan('make:service', [
        'name' => 'GammaService',
        '--facade' => 'Delta',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFileNotContains([
        '// existing facade file',
    ], 'app/Support/Facades/Delta.php');

    $this->assertFileContains([
        'namespace App\\Support\\Facades;',
        'class Delta extends Facade',
    ], 'app/Support/Facades/Delta.php');
});

it('does not write facade when force is enabled but service name is invalid', function () {
    $this->app['files']->put(
        $this->app->basePath('app/Support/Facades/Delta.php'),
        <<<'EOF'
        <?php

        // existing facade file
        EOF
    );

    $this->artisan('make:service', [
        'name' => 'class',
        '--facade' => 'SomeClass',
        '--force' => true,
    ])->assertSuccessful();

    $this->assertFilenameNotExists('app/Support/Services/class.php');
    $this->assertFilenameNotExists('app/Support/Facades/SomeClass.php');
});
