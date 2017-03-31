<?php

namespace PHPPM\Psr7;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPPM\Bootstraps\ApplicationEnvironmentAwareInterface;
use PHPPM\Bootstraps\AsyncInterface;
use PHPPM\Bootstraps\BootstrapInterface;
use PHPPM\Bridges\BridgeInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\LoopInterface;

class Psr7Bridge implements BridgeInterface
{
    /**
     * @var DelegateInterface
     */
    protected $middleware;

    /**
     * Bootstrap an application implementing the HttpKernelInterface.
     *
     * @param string $appBootstrap The name of the class (implementing BootstrapInterface) used to bootstrap the application, must implement DelegateInterface
     * @param string $appenv The environment your application will use to bootstrap (if any)
     * @param boolean $debug If debug is enabled
     * @param LoopInterface $loop
     * @see DelegateInterface
     * @see BootstrapInterface
     * @see http://stackphp.com
     */
    public function bootstrap($appBootstrap, $appenv, $debug, LoopInterface $loop)
    {
        $this->middleware = new $appBootstrap;
        if (!($this->middleware instanceof DelegateInterface)) {
            throw new \RuntimeException(sprintf('%s must implement %s', get_class($this->middleware), DelegateInterface::class));
        }
        if ($this->middleware instanceof ApplicationEnvironmentAwareInterface) {
            $this->middleware->initialize($appenv, $debug);
        }
        if ($this->middleware instanceof AsyncInterface) {
            $this->middleware->setLoop($loop);
        }
    }

    /**
     * Handle a request using a HttpKernelInterface implementing application.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function onRequest(RequestInterface $request)
    {
        return $this->middleware->process($request);
    }
}
