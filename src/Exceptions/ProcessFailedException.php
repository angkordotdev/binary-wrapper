<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Exceptions;

use Illuminate\Contracts\Process\ProcessResult;

class ProcessFailedException extends \RuntimeException
{
    public function __construct(public readonly ProcessResult $result)
    {
        parent::__construct(
            "Process failed with exit code {$result->exitCode()}: {$result->errorOutput()}"
        );
    }
}
