<?php

namespace Elegance\IdentityProvider\Middlewares;

use Elegance\IdentityProvider\Utils\AccessLogger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class AccessLogMiddleware
 *
 * Middleware Slim pour logger automatiquement toutes les requêtes HTTP.
 */
class AccessLogMiddleware implements MiddlewareInterface
{
    protected AccessLogger $accessLogger;

    /**
     * Constructor
     *
     * @param AccessLogger $accessLogger Instance du logger d'accès
     */
    public function __construct(AccessLogger $accessLogger)
    {
        $this->accessLogger = $accessLogger;
    }

    /**
     * Traite la requête entrante et log l'accès
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Logger l'accès
        $this->accessLogger->log($request);

        // Passer la requête au prochain middleware ou route
        return $handler->handle($request);
    }
}
