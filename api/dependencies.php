<?php

use Psr\Container\ContainerInterface;

use App\Controllers\OrdersController;
use App\Controllers\AuthController;

if (!isset($container) || !$container instanceof ContainerInterface) {
    die("Error: Dependency container not initialized. This file should be included by index.php.");
}

$container->set(
    name: AuthController::class,
    value: fn(ContainerInterface $c) => new AuthController(
        dataAccess: $c->get(App\Utility\_DataAccess::class)
    )
);
$container->set(
    name: OrdersController::class,
    value: fn(ContainerInterface $c) => new OrdersController(
        dataAccess: $c->get(App\Utility\_DataAccess::class)
    )
);