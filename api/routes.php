<?php

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