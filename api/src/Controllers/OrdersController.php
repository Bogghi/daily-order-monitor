<?php
namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use App\Controllers\BaseController;

class OrdersController extends BaseController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function listOrders($request, $response, $args)
    {
        $result = new Result();

        if ($this->validateToken($request)) {

        } else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }
}