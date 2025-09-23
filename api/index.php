<?php
use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Middleware\BodyParsingMiddleware;

require __DIR__ . '/vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

require_once __DIR__ . '/env.php';
require_once __DIR__ . '/dependencies.php';
require_once __DIR__ . '/routes.php';

$app->add(new BodyParsingMiddleware());
$app->addRoutingMiddleware();
$app->run();