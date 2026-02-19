<?php

namespace Elegance\IdentityProvider\Utils;

use Psr\Log\LoggerInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class AccessLogger
 *
 * Logger pour suivre les accès aux routes avec IP, méthode HTTP et URI,
 * en ignorant les assets statiques et les fichiers favicon.ico.
 */
class AccessLogger
{
    protected LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger PSR-3 logger instance
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log an access to a route
     *
     * @param ServerRequestInterface $request HTTP request object
     * @param string|null $routeName Optional route name or path
     */
    public function log(ServerRequestInterface $request, ?string $routeName = null): void
    {
        // Récupère le chemin et ignore les assets ou favicon.ico
        $uri = $request->getUri()->getPath();
        if (str_contains($uri, 'assets') || str_ends_with($uri, '.ico')) {
            return; // on ne loggue pas
        }

        $serverParams = $request->getServerParams();
        $ip = $serverParams['REMOTE_ADDR'] ?? ($serverParams['HTTP_X_FORWARDED_FOR'] ?? 'CLI');
        $method = $request->getMethod();
        $route = $routeName ?? $uri;

        $message = sprintf(
            "Access: %s %s from %s",
            $method,
            $route,
            $ip
        );

        $this->logger->info($message, [
            'ip' => $ip,
            'method' => $method,
            'route' => $route,
            'time' => date('Y-m-d H:i:s')
        ]);
    }
}
