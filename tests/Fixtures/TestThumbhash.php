<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Tests\Fixtures;

use Angkor\BinaryWrapper\Thumbhash\Thumbhash;

class TestThumbhash extends Thumbhash
{
    protected function fileExists(string $path): bool
    {
        return $path !== '/no/such/binary';
    }
}
