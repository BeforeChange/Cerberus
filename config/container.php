<?php

use DI\ContainerBuilder;
use Elegance\IdentityProvider\Controllers\Auth\LoginController;
use Elegance\IdentityProvider\Controllers\Auth\LogoutController;
use Elegance\IdentityProvider\Controllers\Auth\RegisterController;
use Elegance\IdentityProvider\Infrastructure\Database\Database;
use Elegance\IdentityProvider\Middlewares\AccessLogMiddleware;
use Elegance\IdentityProvider\Middlewares\RedirectToLoginMiddleware;
use Elegance\IdentityProvider\Models\User;
use Elegance\IdentityProvider\Services\UserService;
use Elegance\IdentityProvider\Utils\AccessLogger;
use Elegance\IdentityProvider\Utils\Logger;
use Psr\Log\LoggerInterface;
use Slim\Views\PhpRenderer;
use function DI\autowire;

$builder = new ContainerBuilder();

$builder->addDefinitions([
    Logger::class => function ($c) {
        return new Logger(__DIR__ . '/../logs/');
    },
    LoggerInterface::class => fn ($c) => $c->get(Logger::class),


    PhpRenderer::class => function($c) {
        return new PhpRenderer(__DIR__ . '/../views', ['title' => 'Elegance OAuth', 'extra_css' => [], 'withMenu' => true], 'layout.php');
    },

    Database::class => function ($c) {
        return new Database(
            $_ENV['DB_HOST'],
            $_ENV['DB_NAME'],
            $_ENV['DB_USER'],
            $_ENV['DB_PASS'],
        );
    },
    PDO::class => fn ($c) => $c->get(Database::class),

    User::class => autowire(),

    UserService::class => autowire(),

    LoginController::class => autowire(),
    RegisterController::class => autowire(),
    LogoutController::class => autowire(),
    AccessLogger::class => autowire(),
    AccessLogMiddleware::class => autowire(),
    RedirectToLoginMiddleware::class => autowire()
]);

$container = $builder->build();

return $container;