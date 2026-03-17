<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Thumbhash;

use Angkor\BinaryWrapper\BinaryServiceProvider;

class ThumbhashServiceProvider extends BinaryServiceProvider
{
    protected function wrapperClass(): string
    {
        return Thumbhash::class;
    }

    protected function configKey(): string
    {
        return 'thumbhash';
    }

    protected function configPath(): string
    {
        return __DIR__.'/../../config/thumbhash.php';
    }
}
