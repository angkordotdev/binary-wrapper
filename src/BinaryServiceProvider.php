<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper;

use Angkor\BinaryWrapper\Console\CheckBinaryCommand;
use Illuminate\Support\ServiceProvider;

abstract class BinaryServiceProvider extends ServiceProvider
{
    /**
     * FQCN of the BinaryWrapper subclass to bind in the container.
     * e.g. \Angkor\BinaryWrapper\Thumbhash\Thumbhash::class
     */
    abstract protected function wrapperClass(): string;

    /**
     * Config key used to look up binary_path.
     * e.g. 'thumbhash' → config('thumbhash.binary_path')
     */
    abstract protected function configKey(): string;

    /**
     * Absolute path to this package's config file.
     * e.g. __DIR__ . '/../config/thumbhash.php'
     */
    abstract protected function configPath(): string;

    public function register(): void
    {
        $this->mergeConfigFrom($this->configPath(), $this->configKey());

        $wrapperClass = $this->wrapperClass();
        $configKey    = $this->configKey();

        $this->app->bind($wrapperClass, fn ($app) => new $wrapperClass(
            config()->string("{$configKey}.binary_path", ''),
        ));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->configPath() => config_path("{$this->configKey()}.php"),
            ], "{$this->configKey()}-config");

            $this->commands([CheckBinaryCommand::class]);
        }
    }
}
