<?php

namespace Determined\LaraDev;

use Determined\LaraDev\Console\FacadeMakeCommand;
use Illuminate\Support\ServiceProvider;

class LaraDevServiceProvider extends ServiceProvider
{
    protected function bootCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                FacadeMakeCommand::class,
            ]);
        }
    }

    protected function bootPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../stubs' => $this->app->basePath('stubs'),
            ], ['laradev:stubs', 'laradev']);
        }
    }

    public function boot(): void
    {
        $this->bootPublishing();
        $this->bootCommands();
    }

    public function register(): void
    {
        //
    }
}
