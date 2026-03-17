<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Tests;

use Angkor\BinaryWrapper\Thumbhash\ThumbhashServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ThumbhashServiceProvider::class,
        ];
    }
}
