<?php

use Determined\LaraDev\Console\ServiceMakeCommand;
use Illuminate\Console\Application;
use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

mutates(ServiceMakeCommand::class);

it('sets the correct command name', function () {
    $files = mock(Filesystem::class);

    $command = new ServiceMakeCommand($files);

    expect($command->getName())->toBe('make:service');
});

it('configures arguments', function () {
    $files = mock(Filesystem::class);

    $definition = (new ServiceMakeCommand($files))->getDefinition();

    expect($definition->hasArgument('name'))->toBeTrue()
        ->and($definition->hasOption('facade'))->toBeTrue()
        ->and($definition->getOption('facade')->getShortcut())->toBe('F')
        ->and($definition->getOption('facade')->isValueRequired())->toBeFalse()
        ->and($definition->getOption('facade')->isValueOptional())->toBeTrue()
        ->and($definition->getOption('facade')->isArray())->toBeFalse()
        ->and($definition->getOption('facade')->isNegatable())->toBeFalse()
        ->and($definition->getOption('facade')->getDescription())->toBe('Create a new facade for the service')
        ->and($definition->hasOption('singleton'))->toBeTrue()
        ->and($definition->getOption('singleton')->getShortcut())->toBe('s')
        ->and($definition->getOption('singleton')->isValueRequired())->toBeFalse()
        ->and($definition->getOption('singleton')->isValueOptional())->toBeFalse()
        ->and($definition->getOption('singleton')->isArray())->toBeFalse()
        ->and($definition->getOption('singleton')->isNegatable())->toBeFalse()
        ->and($definition->getOption('singleton')->getDescription())->toBe('Make a singleton service')
        ->and($definition->hasOption('force'))->toBeTrue()
        ->and($definition->getOption('force')->getShortcut())->toBe('f')
        ->and($definition->getOption('force')->isValueRequired())->toBeFalse()
        ->and($definition->getOption('force')->isValueOptional())->toBeFalse()
        ->and($definition->getOption('force')->isArray())->toBeFalse()
        ->and($definition->getOption('force')->isNegatable())->toBeFalse()
        ->and($definition->getOption('force')->getDescription())
        ->toBe('Create the class even if the service already exists');
});

it('relays relevant arguments to make:facade', function (array $input, array $args) {
    $files = mock(Filesystem::class);

    $command = new class ($files) extends ServiceMakeCommand {
        public array $calls = [];

        #[\Override]
        public function handle()
        {
            $this->createFacade();
        }

        #[\Override]
        public function call($command, array $arguments = [])
        {
            $this->calls[] = compact('command', 'arguments');
        }
    };

    $laravel = mock(Application::class)->makePartial();
    $laravel->shouldReceive('getNamespace')
        ->andReturn('App\\');

    $input = new ArrayInput($input, $command->getDefinition());

    $command->setLaravel($laravel);
    $command->setInput($input);
    $command->setOutput(new OutputStyle($input, new NullOutput()));

    $command->handle();

    expect($command->calls)->toEqual([[
        'command' => 'make:facade',
        'arguments' => $args,
    ]]);
})->with([
    'automatic name' => [
        ['name' => 'FooService', '--facade' => true],
        ['name' => 'Foo', '--target' => 'App\Services\FooService'],
    ],
    'automatic bare name' => [
        ['name' => 'Foo', '--facade' => true],
        ['name' => 'Foo', '--target' => 'App\Services\Foo'],
    ],
    'automatic namespaced name' => [
        ['name' => 'Foo/BarService', '--facade' => true],
        ['name' => 'Foo/Bar', '--target' => 'App\Services\Foo\BarService'],
    ],
    'automatic namespaced bare name' => [
        ['name' => 'Foo/Bar', '--facade' => true],
        ['name' => 'Foo/Bar', '--target' => 'App\Services\Foo\Bar'],
    ],
    'explicit name' => [
        ['name' => 'FooService', '--facade' => 'Bar'],
        ['name' => 'Bar', '--target' => 'App\Services\FooService'],
    ],
    'explicit namespaced facade' => [
        ['name' => 'FooService', '--facade' => 'Bar/Baz'],
        ['name' => 'Bar/Baz', '--target' => 'App\Services\FooService'],
    ],
    'explicit non namespaced facade' => [
        ['name' => 'Foo/BarService', '--facade' => 'Bar'],
        ['name' => 'Bar', '--target' => 'App\Services\Foo\BarService'],
    ],
    'force false' => [
        ['name' => 'FooService', '--facade' => true, '--force' => false],
        ['name' => 'Foo', '--target' => 'App\Services\FooService'],
    ],
    'force true' => [
        ['name' => 'FooService', '--facade' => true, '--force' => true],
        ['name' => 'Foo', '--target' => 'App\Services\FooService', '--force' => true],
    ],
]);
