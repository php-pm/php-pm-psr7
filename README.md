# PHP-PM PSR-7 Adapter

PSR-7 adapter for use of PSR-7 middleware applications with PHP-PM.
See https://github.com/php-pm/php-pm.

## Setup

```
composer require php-pm/psr7-adapter
```

## Usage

When starting PPM, pass your PSR-7 compatible middleware as the bootstrapper.
This will be instantiated once, before it will be re-used for every request:

```
vendor/bin/ppm start --bridge=PHPPM\\Psr7\\Psr7Bridge --bootstrap=Your\\App\\Middleware
```

Alternatively, first configure PPM to use these options by default, and then start it directly:

```
vendor/bin/ppm config --bridge=PHPPM\\Psr7\\Psr7Bridge --bootstrap=Your\\App\\Middleware
vendor/bin/ppm start
```
