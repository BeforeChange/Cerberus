<?php

namespace Elegance\IdentityProvider\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Psr7\Response;

/**
 * Middleware pour rediriger les routes 404 vers /login
 */
class RedirectToLoginMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();

        // Execute handler first to obtain the response
        $response = $handler->handle($request);

        // Si la route n'existe pas (404), redirige vers /login avec gardes
        if ($response->getStatusCode() === 404) {
            // Ne pas rediriger pour /login, assets, API ou pour les requêtes non-HTML/ non-GET
            $method = $request->getMethod();
            $accept = $request->getHeaderLine('Accept') ?? '';

            if (
                $path === '/login'
                || str_starts_with($path, '/assets')
                || str_starts_with($path, '/api')
                || $method !== 'GET'
                || strpos($accept, 'text/html') === false
            ) {
                return $response;
            }

            $redirect = new Response();
            return $redirect
                ->withHeader('Location', '/login')
                ->withStatus(302);
        }

        return $response;
    }
}
