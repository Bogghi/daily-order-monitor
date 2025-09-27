<?php

use App\Controllers\AuthController;
use App\Controllers\OrdersController;
use App\Controllers\ProductsController;

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
$app->post(
    pattern: BASE_ROUTE . "/orders/add",
    callable: OrdersController::class . ':addOrder'
);
$app->post(
    pattern: BASE_ROUTE . "/orders/{order_id}/delete",
    callable: OrdersController::class . ':deleteOrder'
);
$app->post(
    pattern: BASE_ROUTE . "/orders/{order_id}/update",
    callable: OrdersController::class . ':updateOrder'
);
$app->post(
    pattern: BASE_ROUTE . "/orders-items/update",
    callable: OrdersController::class . ':updateOrderItems'
);
$app->get(
    pattern: BASE_ROUTE . "/products",
    callable: ProductsController::class . ':listProducts'
);
$app->post(
    pattern: BASE_ROUTE . "/products/add",
    callable: ProductsController::class . ':addProduct'
);
$app->post(
    pattern: BASE_ROUTE . "/products/{product_id}/delete",
    callable: ProductsController::class . ':deleteProduct'
);