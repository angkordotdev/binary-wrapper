<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper;

use Angkor\BinaryWrapper\Exceptions\BinaryNotFoundException;
use Angkor\BinaryWrapper\Exceptions\ProcessFailedException;
use Illuminate\Contracts\Process\ProcessResult;
use Illuminate\Support\Facades\Process;

abstract class BinaryWrapper
{
    public function __construct(
        protected string $binaryPath,
    ) {}

    /**
     * Bare binary name used as fallback when no path is configured.
     * e.g. 'thumbhash', 'khmercut'
     */
    abstract protected function defaultBinary(): string;

    /**
     * Resolve the binary path — configured value or fallback to defaultBinary().
     */
    public function binaryPath(): string
    {
        return $this->binaryPath ?: $this->defaultBinary();
    }

    /**
     * Run the binary with the given arguments.
     * Validates existence lazily, checks exit code, and returns the result.
     *
     * @param  list<string>  $arguments
     *
     * @throws BinaryNotFoundException
     * @throws ProcessFailedException
     */
    protected function run(array $arguments = []): ProcessResult
    {
        $binary = $this->binaryPath();

        if (! $this->fileExists($binary)) {
            throw new BinaryNotFoundException("Binary not found: {$binary}");
        }

        $result = Process::run([$binary, ...$arguments]);

        if (! $result->successful()) {
            throw new ProcessFailedException($result);
        }

        return $result;
    }

    /**
     * Check if the binary file exists. Overridable for testing.
     */
    protected function fileExists(string $path): bool
    {
        return file_exists($path);
    }
}
