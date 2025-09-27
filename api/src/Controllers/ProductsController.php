<?php
namespace App\Controllers;

use App\Utility\_DataAccess;
use App\Utility\Result;
use App\Controllers\BaseController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductsController extends BaseController
{
    private _DataAccess $dataAccess;

    public function __construct(_DataAccess $dataAccess)
    {
        $this->dataAccess = $dataAccess;
    }

    public function listProducts(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if ($this->validateToken($request)) {
            $products = $this->dataAccess->get(table: "iliad.products");
            $result->setSuccessResult(['products' => $products]);
        } else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function addProduct(Request $request, Response $response, array $args): Response
    {
        $result = new Result();
        $requestBody = $request->getParsedBody();

        if ($this->validateToken($request)) {
            if (isset($requestBody['name']) && isset($requestBody['price'])) {
                $name = $requestBody['name'];
                $price = $requestBody['price'];

                $productId = $this->dataAccess->add(
                    table: 'iliad.products',
                    requestData: [
                        'name' => $name,
                        'price' => $price
                    ]
                );

                if ($productId) {
                    $result->setSuccessResult([
                        'product_id' => $productId,
                        'message' => 'Product created successfully'
                    ]);
                } else {
                    $result->setError('Failed to create product', 500);
                }
            } else {
                $result->setInvalidParameters();
            }
        } else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }

    public function deleteProduct(Request $request, Response $response, array $args): Response
    {
        $result = new Result();

        if ($this->validateToken($request)) {
            if (isset($args['product_id'])) {
                $productId = $args['product_id'];

                $deleted = $this->dataAccess->delete(
                    table: 'iliad.products',
                    args: ['product_id' => $productId]
                );

                if ($deleted) {
                    $result->setSuccessResult(['message' => 'Product deleted successfully']);
                } else {
                    $result->setError('Product not found or could not be deleted', 404);
                }
            } else {
                $result->setInvalidParameters();
            }
        } else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result->data));
        return $response
            ->withStatus($result->statusCode)
            ->withHeader('Content-Type', 'application/json');
    }
}