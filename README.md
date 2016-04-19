# PHP-PM PSR-7 Adapter

PSR-7 adapter for use of PSR-7 middleware applications with PHP-PM.
See https://github.com/php-pm/php-pm.

## Setup

```
composer require php-pm/psr7-adapter
```

## Usage

PPM bootstraps your application once, and then passes all incoming requests to this instance.
This instance needs to be a PSR-7 compatible interface, which means it needs to implement the following interface:

```php
public function __invoke($request, $response, $next = null)
```

So, to be compatible with this adapter, you need to implement a class that, when instantiated, sets up your application, and implements the `__invoke` method as described above.

For example, if you use Zend's [Stratigility library](https://github.com/zendframework/zend-stratigility), your bootstrapper could look like this:

```php
namespace Your\App;

class Middleware
{
    public function __construct()
    {
        // Set up the application
    }

    public function __invoke($request, $response, $next = null)
    {
        // Handle the request and return a new response
    }
}
```

### Starting the server

When starting PPM, pass your middleware as the bootstrapper:

```
vendor/bin/ppm start --bridge=PHPPM\\Psr7\\Psr7Bridge --bootstrap=Your\\App\\Middleware
```

Alternatively, first configure PPM to use these options by default, and then start it directly:

```
vendor/bin/ppm config --bridge=PHPPM\\Psr7\\Psr7Bridge --bootstrap=Your\\App\\Middleware
vendor/bin/ppm start
```
