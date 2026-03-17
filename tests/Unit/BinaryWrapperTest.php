<?php

declare(strict_types=1);

use Angkor\BinaryWrapper\Exceptions\BinaryNotFoundException;
use Angkor\BinaryWrapper\Exceptions\ProcessFailedException;
use Angkor\BinaryWrapper\Tests\Fixtures\FakeBinaryWrapper;
use Illuminate\Support\Facades\Process;

test('binaryPath() returns the configured path', function () {
    $wrapper = new FakeBinaryWrapper('/some/binary');

    expect($wrapper->binaryPath())->toBe('/some/binary');
});

test('binaryPath() falls back to defaultBinary() when empty', function () {
    $wrapper = new FakeBinaryWrapper('');

    expect($wrapper->binaryPath())->toBe('fake-binary');
});

test('run() throws BinaryNotFoundException when binary does not exist', function () {
    $wrapper = new FakeBinaryWrapper('/no/such/binary');

    $wrapper->runCommand([]);
})->throws(BinaryNotFoundException::class);

test('run() throws ProcessFailedException on non-zero exit', function () {
    Process::fake([
        "'fake-binary'" => Process::result(exitCode: 1, errorOutput: 'something went wrong'),
    ]);

    $wrapper = new FakeBinaryWrapper('');

    $wrapper->runCommand([]);
})->throws(ProcessFailedException::class);

test('run() returns ProcessResult on success', function () {
    Process::fake([
        "'fake-binary'" => Process::result('success output'),
    ]);

    $wrapper = new FakeBinaryWrapper('');
    $result  = $wrapper->runCommand([]);

    expect($result->output())->toContain('success output');
});
