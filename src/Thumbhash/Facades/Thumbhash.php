<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Thumbhash\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string encode(string $imagePath)
 * @method static void decode(string $hash, string $outputPath, ?int $size = null)
 * @method static string toRawData(string $imagePath, ?string $outputPath = null)
 * @method static string binaryPath()
 *
 * @see \Angkor\BinaryWrapper\Thumbhash\Thumbhash
 */
class Thumbhash extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Angkor\BinaryWrapper\Thumbhash\Thumbhash::class;
    }
}
