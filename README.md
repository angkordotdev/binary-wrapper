# angkor/binary-wrapper

Base abstraction for Laravel packages that wrap Go/Rust CLI binaries.

## Overview

This package provides the scaffolding to build Laravel packages around external CLI binaries (Go, Rust, etc.). It handles binary resolution, process execution, error handling, config publishing, and includes a ready-to-use Thumbhash implementation as a reference.

## Requirements

- PHP 8.3+
- Laravel 11 or 12

## Installation

```bash
composer require angkor/binary-wrapper
```

## Usage

### Building your own binary wrapper

**1. Extend `BinaryWrapper`**

```php
use Angkor\BinaryWrapper\BinaryWrapper;

class MyTool extends BinaryWrapper
{
    protected function defaultBinary(): string
    {
        return 'mytool'; // fallback binary name on PATH
    }

    public function process(string $input): string
    {
        return trim($this->run(['--flag', $input])->output());
    }
}
```

**2. Extend `BinaryServiceProvider`**

```php
use Angkor\BinaryWrapper\BinaryServiceProvider;

class MyToolServiceProvider extends BinaryServiceProvider
{
    protected function wrapperClass(): string
    {
        return MyTool::class;
    }

    protected function configKey(): string
    {
        return 'mytool';
    }

    protected function configPath(): string
    {
        return __DIR__ . '/../config/mytool.php';
    }
}
```

**3. Create a config file** (`config/mytool.php`)

```php
return [
    'binary_path' => env('MYTOOL_BINARY_PATH', '/usr/local/bin/mytool'),
];
```

The service provider will automatically:
- Merge config from your package
- Bind `MyTool` in the container, injecting `binary_path` from config
- Publish config and register the `binary:check` command when running in console

### Publish config

```bash
php artisan vendor:publish --tag=mytool-config
```

### Check binary accessibility

```bash
php artisan binary:check "App\MyTool"
```

## Thumbhash

This package ships with a Thumbhash implementation that wraps the [go-thumbhash](https://github.com/angkordotdev/go-thumbhash) CLI binary.

### Setup

Register the service provider in `config/app.php` (auto-discovered via composer.json):

```php
Angkor\BinaryWrapper\Thumbhash\ThumbhashServiceProvider::class,
```

Set the binary path in your `.env`:

```env
THUMBHASH_BINARY_PATH=/usr/local/bin/thumbhash
```

Or publish and edit the config:

```bash
php artisan vendor:publish --tag=thumbhash-config
```

### API

```php
use Angkor\BinaryWrapper\Thumbhash\Facades\Thumbhash;

// Encode an image to a base64 thumbhash string
$hash = Thumbhash::encode('/path/to/image.jpg');

// Decode a thumbhash back to a PNG
Thumbhash::decode($hash, '/path/to/output.png');
Thumbhash::decode($hash, '/path/to/output.png', size: 32);

// Convert image to raw RGBA data
$dataPath = Thumbhash::toRawData('/path/to/image.jpg');
$dataPath = Thumbhash::toRawData('/path/to/image.jpg', '/path/to/output.data');
```

## Exceptions

| Exception | Thrown when |
|---|---|
| `BinaryNotFoundException` | Binary file does not exist at resolved path |
| `ProcessFailedException` | Binary exits with a non-zero status code |

## Testing

```bash
composer test
composer analyse
composer format
```

## License

MIT
