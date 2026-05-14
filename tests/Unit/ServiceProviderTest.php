<?php

use Determined\LaraDev\Console\FacadeMakeCommand;
use Determined\LaraDev\LaraDevServiceProvider;
use Illuminate\Support\ServiceProvider;

mutates(\Determined\LaraDev\LaraDevServiceProvider::class);

beforeEach(function () {
    ServiceProvider::$publishes = [];
    ServiceProvider::$publishGroups = [];
});

it('registers commands when running in console', function () {
    $app = mock(\Illuminate\Contracts\Foundation\Application::class);

    $app->shouldReceive('runningInConsole')->andReturnTrue();

    $provider = new class ($app) extends LaraDevServiceProvider {
        public static $commands;

        #[\Override]
        protected function bootPublishing(): void
        {
            //
        }

        #[\Override]
        public function commands($commands)
        {
            self::$commands = $commands;
        }
    };

    $provider->boot();

    expect($provider::$commands)->toEqual([
        FacadeMakeCommand::class,
    ]);
});

it('does not register commands when not running in console', function () {
    $app = mock(\Illuminate\Contracts\Foundation\Application::class);

    $app->shouldReceive('runningInConsole')->andReturnFalse();

    $provider = new class ($app) extends LaraDevServiceProvider {
        public static $commands;

        #[\Override]
        protected function bootPublishing(): void
        {
            //
        }

        #[\Override]
        public function commands($commands)
        {
            self::$commands = $commands;
        }
    };

    $provider->boot();

    expect($provider::$commands)->toBeEmpty();
});

it('registers publishable assets when running in console', function () {
    $app = mock(\Illuminate\Contracts\Foundation\Application::class);

    $app->shouldReceive('runningInConsole')->andReturnTrue();
    $app->shouldReceive('basePath')->with('stubs')->andReturn('/test/stubs');

    $provider = new class ($app) extends LaraDevServiceProvider {
        #[\Override]
        protected function bootCommands(): void
        {
            //
        }
    };

    $provider->boot();

    $paths = [realpath(__DIR__ . '/../../') . '/src/../stubs' => '/test/stubs'];

    expect($provider::$publishes)->toEqual([
        $provider::class => $paths
    ])
        ->and($provider::$publishGroups)->toEqual([
            'laradev:stubs' => $paths,
            'laradev' => $paths,
        ]);
});

it('does not register publishable assets when not running in console', function () {
    $app = mock(\Illuminate\Contracts\Foundation\Application::class);

    $app->shouldReceive('runningInConsole')->andReturnFalse();
    $app->shouldReceive('basePath')->never();

    $provider = new class ($app) extends LaraDevServiceProvider {
        #[\Override]
        protected function bootCommands(): void
        {
            //
        }
    };

    $provider->boot();

    expect($provider::$publishes)->toBeEmpty()
        ->and($provider::$publishGroups)->toBeEmpty();
});
