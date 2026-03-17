<?php

declare(strict_types=1);

use Angkor\BinaryWrapper\Exceptions\BinaryNotFoundException;
use Angkor\BinaryWrapper\Tests\Fixtures\TestThumbhash;
use Angkor\BinaryWrapper\Thumbhash\Facades\Thumbhash as ThumbhashFacade;
use Angkor\BinaryWrapper\Thumbhash\Thumbhash;
use Illuminate\Support\Facades\Process;

// Symfony quotes array commands as "'binary' 'arg'" — use this prefix for fake patterns.
const THUMBHASH_BIN = "'/usr/local/bin/thumbhash'";

beforeEach(function () {
    // Rebind Thumbhash to TestThumbhash so fileExists() doesn't hit the real filesystem.
    app()->bind(Thumbhash::class, fn ($app) => new TestThumbhash(
        (string) $app->make('config')->get('thumbhash.binary_path', ''),
    ));
});

test('container resolves Thumbhash instance', function () {
    expect(app(Thumbhash::class))->toBeInstanceOf(Thumbhash::class);
});

test('config is loaded with correct defaults', function () {
    expect(config('thumbhash.binary_path'))->toBe('/usr/local/bin/thumbhash');
});

test('encode() calls binary with encode-image and returns trimmed output', function () {
    Process::fake([THUMBHASH_BIN.' *' => Process::result("abc123hash\n")]);

    $hash = app(Thumbhash::class)->encode('/path/to/image.jpg');

    expect($hash)->toBe('abc123hash');

    Process::assertRan(fn ($process) => $process->command[0] === '/usr/local/bin/thumbhash'
        && in_array('encode-image', $process->command)
        && in_array('/path/to/image.jpg', $process->command));
});

test('decode() calls binary with decode-image, output path, and hash', function () {
    Process::fake([THUMBHASH_BIN.' *' => Process::result('')]);

    app(Thumbhash::class)->decode('abc123hash', '/tmp/output.png');

    Process::assertRan(fn ($process) => in_array('decode-image', $process->command)
        && in_array('/tmp/output.png', $process->command)
        && in_array('abc123hash', $process->command));
});

test('decode() passes size flag when provided', function () {
    Process::fake([THUMBHASH_BIN.' *' => Process::result('')]);

    app(Thumbhash::class)->decode('abc123hash', '/tmp/output.png', 32);

    Process::assertRan(fn ($process) => in_array('-s', $process->command)
        && in_array('32', $process->command));
});

test('toRawData() returns default output path when none specified', function () {
    Process::fake([THUMBHASH_BIN.' *' => Process::result('')]);

    $result = app(Thumbhash::class)->toRawData('/path/to/image.jpg');

    expect($result)->toBe('image.data');
});

test('toRawData() returns provided output path', function () {
    Process::fake([THUMBHASH_BIN.' *' => Process::result('')]);

    $result = app(Thumbhash::class)->toRawData('/path/to/image.jpg', '/tmp/out.data');

    expect($result)->toBe('/tmp/out.data');

    Process::assertRan(fn ($process) => in_array('-o', $process->command)
        && in_array('/tmp/out.data', $process->command));
});

test('BinaryNotFoundException raised when binary path does not exist', function () {
    app()->bind(Thumbhash::class, fn () => new TestThumbhash('/no/such/binary'));

    app(Thumbhash::class)->encode('/path/to/image.jpg');
})->throws(BinaryNotFoundException::class);

test('Facade resolves and proxies encode()', function () {
    Process::fake([THUMBHASH_BIN.' *' => Process::result("facadehash\n")]);

    expect(ThumbhashFacade::encode('/path/to/image.jpg'))->toBe('facadehash');
});
