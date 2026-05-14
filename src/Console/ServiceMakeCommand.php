<?php

namespace Determined\LaraDev\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:service', description: 'Create a new service')]
class ServiceMakeCommand extends GeneratorCommand
{
    protected $type = 'Service';

    protected function getStub(): string
    {
        return $this->option('singleton')
            ? $this->resolveStubPath('/stubs/service.singleton.stub')
            : $this->resolveStubPath('/stubs/service.stub');
    }

    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath($stub))
            ? $customPath
            : __DIR__ . '/../../' . $stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\Services';
    }

    /**
     * @return array<int, string|int>[]
     */
    protected function getOptions(): array
    {
        return [
            ['facade', 'F', InputOption::VALUE_OPTIONAL, 'Create a new facade for the service'],
            ['singleton', 's', InputOption::VALUE_NONE, 'Make a singleton service'],
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the service already exists'],
        ];
    }

    protected function createFacade(): void
    {
        $input = $this->getNameInput();
        $target = $this->qualifyClass($input);

        $name = match (true) {
            is_string($this->option('facade')) => $this->option('facade'),
            Str::endsWith($input, 'Service') => Str::substr($input, 0, -7),
            default => $input,
        };

        $this->call('make:facade', array_filter([
            'name' => $name,
            '--target' => $target,
            '--force' => $this->option('force'),
        ]));
    }

    /**
     * @return bool|void
     */
    public function handle()
    {
        if (parent::handle() === false) {
            return false;
        }

        if ($this->option('facade')) {
            $this->createFacade();
        }
    }
}
