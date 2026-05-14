<?php

use Illuminate\Filesystem\Filesystem;

mutates(\Determined\LaraDev\Console\FacadeMakeCommand::class);

it('sets the correct command name', function () {
    $files = mock(Filesystem::class);

    $command = new \Determined\LaraDev\Console\FacadeMakeCommand($files);

    expect($command->getName())->toBe('make:facade');
});

it('configures arguments', function () {
    $files = mock(Filesystem::class);

    $definition = (new \Determined\LaraDev\Console\FacadeMakeCommand($files))->getDefinition();

    expect($definition->hasArgument('name'))->toBeTrue()
        ->and($definition->hasOption('target'))->toBeTrue()
        ->and($definition->getOption('target')->getShortcut())->toBe('t')
        ->and($definition->getOption('target')->isValueRequired())->toBeTrue()
        ->and($definition->getOption('target')->isValueOptional())->toBeFalse()
        ->and($definition->getOption('target')->isArray())->toBeFalse()
        ->and($definition->getOption('target')->isNegatable())->toBeFalse()
        ->and($definition->getOption('target')->getDescription())->toBe('Set the target service class')
        ->and($definition->hasOption('accessor'))->toBeTrue()
        ->and($definition->getOption('accessor')->getShortcut())->toBe('a')
        ->and($definition->getOption('accessor')->isValueRequired())->toBeTrue()
        ->and($definition->getOption('accessor')->isValueOptional())->toBeFalse()
        ->and($definition->getOption('accessor')->isArray())->toBeFalse()
        ->and($definition->getOption('accessor')->isNegatable())->toBeFalse()
        ->and($definition->getOption('accessor')->getDescription())->toBe('Set the facade accessor')
        ->and($definition->hasOption('force'))->toBeTrue()
        ->and($definition->getOption('force')->getShortcut())->toBe('f')
        ->and($definition->getOption('force')->isValueRequired())->toBeFalse()
        ->and($definition->getOption('force')->isValueOptional())->toBeFalse()
        ->and($definition->getOption('force')->isArray())->toBeFalse()
        ->and($definition->getOption('force')->isNegatable())->toBeFalse()
        ->and($definition->getOption('force')->getDescription())
        ->toBe('Create the facade even if the class already exists');
});
