<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('exceptions extend RuntimeException')
    ->expect('Angkor\BinaryWrapper\Exceptions')
    ->toExtend(RuntimeException::class);
