<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Tests\Fixtures;

use Angkor\BinaryWrapper\BinaryWrapper;
use Illuminate\Contracts\Process\ProcessResult;

class FakeBinaryWrapper extends BinaryWrapper
{
    protected function defaultBinary(): string
    {
        return 'fake-binary';
    }

    public function runCommand(array $arguments = []): ProcessResult
    {
        return $this->run($arguments);
    }

    protected function fileExists(string $path): bool
    {
        return $path !== '/no/such/binary';
    }
}
