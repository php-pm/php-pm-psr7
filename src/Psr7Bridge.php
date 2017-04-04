<?php

namespace PHPPM\Psr7;

use Interop\Http\ServerMiddleware\DelegateInterface;
use PHPPM\Bootstraps\ApplicationEnvironmentAwareInterface;
use PHPPM\Bootstraps\AsyncInterface;
use PHPPM\Bootstraps\BootstrapInterface;
use PHPPM\Bridges\BridgeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\LoopInterface;

class Psr7Bridge implements BridgeInterface
{
    /**
     * @var DelegateInterface
     */
    protected $delegate;

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
        $this->delegate = new $appBootstrap;
        if (!($this->delegate instanceof DelegateInterface)) {
            throw new \RuntimeException(sprintf('%s must implement %s', get_class($this->delegate), DelegateInterface::class));
        }
        if ($this->delegate instanceof ApplicationEnvironmentAwareInterface) {
            $this->delegate->initialize($appenv, $debug);
        }
        if ($this->delegate instanceof AsyncInterface) {
            $this->delegate->setLoop($loop);
        }
    }

    /**
     * Dispatch the next available middleware and return the response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request)
    {
        return $this->delegate->process($request);
    }
}
