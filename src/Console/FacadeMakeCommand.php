<?php

namespace Determined\LaraDev\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:facade', description: 'Create a new facade')]
class FacadeMakeCommand extends GeneratorCommand
{
    protected $type = 'Facade';

    protected function getStub(): string
    {
        return $this->hasTargetClass()
            ? $this->resolveStubPath('/stubs/facade.targeted.stub')
            : $this->resolveStubPath('/stubs/facade.stub');
    }

    protected function getAccessor(): string
    {
        return $this->option('accessor')
            ?: $this->getTargetClass()
            ?: Str::slug($this->getNameInput());
    }

    protected function hasTargetClass(): bool
    {
        return class_exists($this->getTargetClass());
    }

    protected function getTargetClass(): string
    {
        $name = str_replace('/', '\\', $this->getNameInput());
        $rootNamespace = trim($this->rootNamespace(), '\\');

        return $this->option('target')
            ?: array_first(array_filter([
                $this->option('accessor') ?? 'dummy class',
                $rootNamespace . '\\Services\\' . $name . 'Service',
                $rootNamespace . '\\Services\\' . $name,
                $rootNamespace . '\\Services\\' . class_basename($name) . 'Service',
                $rootNamespace . '\\Services\\' . class_basename($name),
                $rootNamespace . '\\' . $name . 'Service',
                $rootNamespace . '\\' . $name,
            ], class_exists(...)))
            ?: '';
    }

    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath($stub))
            ? $customPath
            : __DIR__ . '/../../' . $stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Support\Facades';
    }

    protected function buildClass($name)
    {
        $replace = $this->buildFacadeReplacements();

        return str_replace(
            array_keys($replace),
            $replace,
            parent::buildClass($name)
        );
    }

    /**
     * @return array<string, string>
     */
    protected function buildFacadeReplacements(): array
    {
        $accessor = $this->getAccessor();

        $replacements = [
            '{{ accessor }}' => class_exists($accessor)
                ? Str::wrap($accessor, '\\', '::class')
                : Str::wrap($accessor, '\''),
        ];

        if ($this->hasTargetClass()) {
            $target = $this->getTargetClass();
            $qualified = $target;
            $alias = class_basename($target);

            if ($alias === class_basename($this->getNameInput())) {
                $alias = $alias . 'Service';
                $qualified .= ' as ' . $alias;
            }

            $replacements['{{ accessor }}'] = is_a($accessor, $target, true)
                ? class_basename($alias) . '::class'
                : $replacements['{{ accessor }}'];
            $replacements['{{ target }}'] = $qualified;
            $replacements['{{ alias }}'] = $alias;
        }

        return $replacements;
    }

    /**
     * @return array<int, string|int>[]
     */
    protected function getOptions(): array
    {
        return [
            ['target', 't', InputOption::VALUE_REQUIRED, 'Set the target service class'],
            ['accessor', 'a', InputOption::VALUE_REQUIRED, 'Set the facade accessor'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the facade already exists'],
        ];
    }
}
