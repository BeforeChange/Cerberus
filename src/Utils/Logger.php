<?php

namespace Elegance\IdentityProvider\Utils;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stringable;

/**
 * Class Logger
 *
 * Logger amélioré pour l'application Elegance.
 * - Logs SQL avec paramètres
 * - Logs des accès aux routes avec IP et méthode
 * - Vérifie les permissions sur le fichier log
 */
class Logger implements LoggerInterface
{
    private string $logFile;

    public function __construct(string $logDir)
    {
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        $this->logFile = rtrim($logDir, '/') . '/app.log';

        // Vérifie qu'on peut écrire dans le fichier log
        if (!file_exists($this->logFile)) {
            touch($this->logFile);
            chmod($this->logFile, 0664);
        }
        if (!is_writable($this->logFile)) {
            throw new \RuntimeException("Le fichier de log {$this->logFile} n'est pas accessible en écriture.");
        }
    }

    public function emergency($message, array $context = []): void { $this->log(LogLevel::EMERGENCY, $message, $context); }
    public function alert($message, array $context = []): void { $this->log(LogLevel::ALERT, $message, $context); }
    public function critical($message, array $context = []): void { $this->log(LogLevel::CRITICAL, $message, $context); }
    public function error($message, array $context = []): void { $this->log(LogLevel::ERROR, $message, $context); }
    public function warning($message, array $context = []): void { $this->log(LogLevel::WARNING, $message, $context); }
    public function notice($message, array $context = []): void { $this->log(LogLevel::NOTICE, $message, $context); }
    public function info($message, array $context = []): void { $this->log(LogLevel::INFO, $message, $context); }
    public function debug($message, array $context = []): void { $this->log(LogLevel::DEBUG, $message, $context); }

    /**
     * Logs with an arbitrary level.
     */
    public function log($level, $message, array $context = []): void
    {
        if ($message instanceof Stringable) {
            $message = (string) $message;
        }

        // Remplace les placeholders par le contexte
        foreach ($context as $key => $value) {
            if (is_scalar($value)) {
                $message = str_replace("{{$key}}", (string) $value, $message);
            }
        }

        $line = sprintf(
            "[%s] %s: %s%s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            PHP_EOL
        );

        // Écriture dans le fichier log
        file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }

    /**
     * Log SQL avec paramètres
     */
    public function sql(string $query, array $params = []): void
    {
        $this->debug('[SQL]', ['query' => $query, 'params' => $params]);
    }

    /**
     * Log accès route avec IP et méthode
     */
    public function access(string $route, ?string $method = null, ?string $ip = null): void
    {
        $method = $method ?? ($_SERVER['REQUEST_METHOD'] ?? 'CLI');
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');

        $this->info('[ACCESS]', [
            'route' => $route,
            'method' => $method,
            'ip' => $ip
        ]);
    }
}
