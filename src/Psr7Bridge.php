<?php

namespace PHPPM\Bridges;

use PHPPM\Bootstraps\ApplicationEnvironmentAwareInterface;
use PHPPM\Bootstraps\AsyncInterface;
use PHPPM\Bridges\BridgeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use RingCentral\Psr7;
use React\EventLoop\LoopInterface;

class Psr7Bridge implements BridgeInterface
{
    protected $middleware;

    /**
     * Bootstrap an application implementing the PSR15 RequestHandler interface.
     *
     * @param string $appBootstrap The name of the class used to bootstrap the application
     * @param string|null $appBootstrap The environment your application will use to bootstrap (if any)
     * @param boolean $debug If debug is enabled
     */
    public function bootstrap($appBootstrap, $appenv, $debug, LoopInterface $loop)
    {
        $this->middleware = new $appBootstrap;
        if ($this->middleware instanceof ApplicationEnvironmentAwareInterface) {
            $this->middleware->initialize($appenv, $debug);
        }
        if ($this->middleware instanceof AsyncInterface) {
            $this->middleware->setLoop($loop);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ServerRequestInterface $request)
    {
        if ($this->middleware === null) {
            // internal server error
            return new Psr7\Response(500, ['Content-type' => 'text/plain'], 'Application not configured during bootstrap');
        }

        $middleware = $this->middleware;
        return $middleware($request);
    }
}
