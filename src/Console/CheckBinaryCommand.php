<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Console;

use Angkor\BinaryWrapper\BinaryWrapper;
use Angkor\BinaryWrapper\Exceptions\BinaryNotFoundException;
use Illuminate\Console\Command;

class CheckBinaryCommand extends Command
{
    protected $signature = 'binary:check {class : Fully qualified class name of a BinaryWrapper subclass}';

    protected $description = 'Check whether the configured binary for a wrapper class is accessible';

    public function handle(): int
    {
        $class = $this->argument('class');

        if (! is_string($class) || ! class_exists($class)) {
            $this->error('Class ['.(is_string($class) ? $class : '').'] not found.');

            return self::FAILURE;
        }

        if (! is_subclass_of($class, BinaryWrapper::class)) {
            $this->error("Class [{$class}] is not a BinaryWrapper subclass.");

            return self::FAILURE;
        }

        /** @var BinaryWrapper $instance */
        $instance = app($class);
        $path     = $instance->binaryPath();

        try {
            if (! file_exists($path)) {
                throw new BinaryNotFoundException("Binary not found: {$path}");
            }

            $this->info("Binary found: {$path}");

            return self::SUCCESS;
        } catch (BinaryNotFoundException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}
