<?php

declare(strict_types=1);

namespace Angkor\BinaryWrapper\Thumbhash;

use Angkor\BinaryWrapper\BinaryWrapper;

class Thumbhash extends BinaryWrapper
{
    protected function defaultBinary(): string
    {
        return 'thumbhash';
    }

    /**
     * Encode an image file to a base64 thumbhash string.
     */
    public function encode(string $imagePath): string
    {
        return trim($this->run(['encode-image', $imagePath])->output());
    }

    /**
     * Decode a base64 thumbhash string to a PNG image file.
     */
    public function decode(string $hash, string $outputPath, ?int $size = null): void
    {
        $args = ['decode-image', $outputPath, $hash];

        if ($size !== null) {
            $args[] = '-s';
            $args[] = (string) $size;
        }

        $this->run($args);
    }

    /**
     * Convert an image to raw RGBA data.
     * Returns the output file path.
     */
    public function toRawData(string $imagePath, ?string $outputPath = null): string
    {
        $args = ['image-to-raw-data', $imagePath];

        if ($outputPath !== null) {
            $args[] = '-o';
            $args[] = $outputPath;
        }

        $this->run($args);

        return $outputPath ?? pathinfo($imagePath, PATHINFO_FILENAME).'.data';
    }
}
