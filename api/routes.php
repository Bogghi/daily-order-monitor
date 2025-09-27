<?php

use App\Controllers\AuthController;
use App\Controllers\OrdersController;

if (!isset($app)) {
    exit();
}

const BASE_ROUTE = '/API/v1';

$app->get(
    pattern: BASE_ROUTE . "/",
    callable: function ($request, $response, $args) {
        $response->getBody()->write("Welcome to the Iliad API v1");
        return $response;
    }
);

$app->post(
    pattern: BASE_ROUTE . "/login",
    callable: AuthController::class . ':login'
);
$app->post(
    pattern: BASE_ROUTE . "/register",
    callable: AuthController::class . ':register'
);
$app->get(
    pattern: BASE_ROUTE . "/orders",
    callable: OrdersController::class . ':listOrders'
);