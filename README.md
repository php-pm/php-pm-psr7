# PHP-PM PSR-7 Adapter

[PSR-7](http://www.php-fig.org/psr/psr-7/) adapter for use of PSR-7 middleware applications with PHP-PM.
See https://github.com/php-pm/php-pm.

## Setup

```
composer require php-pm/psr7-adapter
```

## Usage

PPM bootstraps your application once, and then passes all incoming requests to this instance.
This instance needs to be a [PSR-15 DelegateInterface](https://github.com/php-fig/fig-standards/blob/master/proposed/http-middleware/middleware.md) compatible, which means it needs to implement the following method:

```php
/**
* @return Psr\Http\Message\ResponseInterface
**/
public function process(Psr\Http\Message\ServerRequestInterface $request)
```

So, to be compatible with this adapter, you need to implement a class that, when instantiated, sets up your application, and implements the `process` method as described above.

For example, if you use Zend's [Stratigility library](https://github.com/zendframework/zend-stratigility), your bootstrapper could look like this:

```php
namespace Your\App;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Stratigility\MiddlewarePipe;
use Zend\Stratigility\Middleware\NotFoundHandler;

class Application implements DelegateInterface
{
    /**
    * @var MiddlewarePipe
    */
    protected $pipe;

    public function __construct()
    {
        // Set up the application

        $this->pipe = new MiddlewarePipe;

        $this->pipe->pipe(new MyFirstMiddleware);
        $this->pipe->pipe(new MySecondMiddleware);
        $this->pipe->pipe(new MyThirdMiddleware);
    }

    /**
    * @param ServerRequestInterface $request
    * @return ResponseInterface
    */
    public function process(ServerRequestInterface $request)
    {
        return $this->pipe->process($request, new NotFoundHandler(new Response()));
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
