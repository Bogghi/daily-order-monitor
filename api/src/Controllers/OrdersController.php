<?php
namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Controllers\BaseController;

class OrdersController extends BaseController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }
}