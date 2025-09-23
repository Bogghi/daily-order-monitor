<?php

use Psr\Container\ContainerInterface;

if (!isset($container) || !$container instanceof ContainerInterface) {
    die("Error: Dependency container not initialized. This file should be included by index.php.");
}