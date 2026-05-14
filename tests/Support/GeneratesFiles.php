<?php

namespace Tests\Support;

use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;

trait GeneratesFiles
{
    use InteractsWithPublishedFiles;

    /** @var string[] */
    protected array $files = [];
}
