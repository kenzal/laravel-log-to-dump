A micro-package for Laravel that allows you to log messages to the `dump` channel, which sends all log messages to the
`dump()` function. This is useful for debugging purposes, especially when you want to see the output of your logs in the
browser or console without having to set up a dedicated logging channel.

Additionally, it may come in handy if you intercept dumped messages (via browser extension, dump server, or a utility
such as [Laravel Herd](https://herd.laravel.com/)).

# Installation

You can install the package via composer (primarily intended for development purposes):

```bash
composer require --dev kenzal/log-to-dump
```

# Usage

Simply set your default log channel to `dump` or a stack that includes `dump`;

You may also specify the `dump` channel in your `config/logging.php` file:

```php
'channels' => [
    // ...
    'dump' => [
        'driver' => 'dump',
    ],
],
```

# Options

You can configure the `dump` channel in your `config/logging.php` file. The following options are available:

- `level`: The minimum log level to log. Defaults to `debug`.
- `formatter`: A monolog formatter to use.
- `processors`: An array of monolog processors to use.

# Testing
```bash
composer test
```

# Contribution

If you want to contribute code to this package, please open an issue first. To avoid unnecessary effort for you, it is
very beneficial to first discuss the idea, the functionality and its API.

# Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

# Security Vulnerabilities

If you discover any security related issues, please submit them through the issue tracker.

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
