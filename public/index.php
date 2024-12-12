<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Alex\TaskManagerApp\Router;

function view($view): void
{
    include __DIR__ . "/../src/pages/view/{$view}.php";
}

function handler($handler): void
{
    include __DIR__ . "/../src/pages/handlers/{$handler}.php";
}

$router = new Router();

$router->addRoute('GET', '/', function () {
    view("login");
});

$router->addRoute('GET', '/register', function () {
    view("register");
});

$router->addRoute('GET', '/login', function () {
    view("login");
});

$router->addRoute('GET', '/profile', function () {
    view("profile");
});

$router->addRoute('GET', '/tasks', function () {
    view("tasks");
});

$router->addRoute('POST', '/tasks', function () {
    handler("tasks_handler");
});

$router->addRoute('DELETE', '/tasks', function () {
    handler("tasks_handler");
});

$router->addRoute('PUT', '/tasks', function () {
    handler("tasks_handler");
});

$router->addRoute('POST', '/profile', function () {
    handler("profile_handler");
});

$router->addRoute('POST', '/login', function () {
    handler("login_handler");
});

$router->addRoute('POST', '/register', function () {
    handler("register_handler");
});

$router->addRoute('POST', '/logout', function () {
    handler("logout_handler");
});

$router->dispatch();