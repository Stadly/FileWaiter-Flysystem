# FileWaiter-Flysystem

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

[Flysystem](https://flysystem.thephpleague.com) file adapter for [FileWaiter](https://github.com/Stadly/FileWaiter).

## Install

Via Composer

``` bash
$ composer require stadly/file-waiter-flysystem
```

## Usage

``` php
use Stadly\FileWaiter\Adapter\Flysystem;
use Stadly\FileWaiter\File;
use Stadly\FileWaiter\Waiter;

$flysystem = new \League\Flysystem\Filesystem($adapter);    // Any Flysystem adapter.
$filePath = '/path/to/file/in/flysystem';

$streamFactory = new \GuzzleHttp\Psr7\HttpFactory();        // Any PSR-17 compatible stream factory.
$file = new File(new Flysystem($flysystem, $filePath, $streamFactory));

$responseFactory = new \GuzzleHttp\Psr7\HttpFactory();      // Any PSR-17 compatible response factory.
$waiter = new Waiter($file, $responseFactory);

// Serve the file stored in Flysystem using FileWaiter.
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email magnar@myrtveit.com instead of using the issue tracker.

## Credits

- [Magnar Ovedal Myrtveit][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [LICENSE](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/stadly/file-waiter-flysystem.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/Stadly/FileWaiter-Flysystem/main.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/Stadly/FileWaiter-Flysystem.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/Stadly/FileWaiter-Flysystem.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/stadly/file-waiter-flysystem.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/stadly/file-waiter-flysystem
[link-travis]: https://app.travis-ci.com/github/Stadly/FileWaiter-Flysystem
[link-scrutinizer]: https://scrutinizer-ci.com/g/Stadly/FileWaiter-Flysystem/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/Stadly/FileWaiter-Flysystem
[link-downloads]: https://packagist.org/packages/stadly/file-waiter-flysystem
[link-author]: https://github.com/Stadly
[link-contributors]: https://github.com/Stadly/FileWaiter-Flysystem/contributors
