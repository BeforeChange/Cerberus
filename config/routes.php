<?php

use Elegance\IdentityProvider\Controllers\HomeController;
use Elegance\IdentityProvider\Controllers\Auth\LoginController;
use Elegance\IdentityProvider\Controllers\Auth\LogoutController;
use Elegance\IdentityProvider\Controllers\Auth\RegisterController;


return function ($app) {
    $app->get('/', [HomeController::class, "show"]);

    $app->get('/register', [RegisterController::class, "show"]);
    $app->post('/register', [RegisterController::class, "register"]);
    
    $app->get('/login', [LoginController::class, "show"]);
    $app->post('/login', [LoginController::class, "login"]);
    
    $app->get('/logout', [LogoutController::class, "logout"]);
};