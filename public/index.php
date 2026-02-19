<?php

require __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Elegance\IdentityProvider\Middlewares\AccessLogMiddleware;
use Elegance\IdentityProvider\Middlewares\RedirectToLoginMiddleware;
use Elegance\IdentityProvider\Utils\Lang;

$lang = new Lang(__DIR__ . '/../lang/en.yml');
$_LANG = $lang;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use Slim\Factory\AppFactory;

$container = require __DIR__ . '/../config/container.php';

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addErrorMiddleware(true, true, true);

$app->add($container->get(RedirectToLoginMiddleware::class));
$app->add($container->get(AccessLogMiddleware::class));

(require __DIR__ . '/../config/routes.php')($app);

$app->run();