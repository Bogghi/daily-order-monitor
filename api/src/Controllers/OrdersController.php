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

            $orders = $this->dataAccess->get(table: "iliad.orders");

            // Fetch order items for each order
            foreach ($orders as &$order) {
                $orderItems = $this->dataAccess->get(
                    table: "iliad.order_items",
                    args: ["order_id" => $order['order_id']]
                );
                $order['order_items'] = $orderItems;
            }

            $result->setSuccessResult(['orders' => $orders]);

        } else {
            $result->setUnauthorized();
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function addOrder($request, $response, $args)
    {
        $result = new Result();
        $requestBody = $request->getParsedBody();

        if ($this->validateToken($request)) {
            if (isset($requestBody['name']) && isset($requestBody['value']) && isset($requestBody['order_items'])) {
                $name = $requestBody['name'];
                $description = $requestBody['description'] ?? null;
                $value = $requestBody['value'];
                $orderItems = $requestBody['order_items'];

                // Create the order
                $orderId = $this->dataAccess->add(
                    table: 'iliad.orders',
                    requestData: [
                        'name' => $name,
                        'description' => $description,
                        'value' => $value
                    ]
                );

                if ($orderId && !empty($orderItems)) {
                    // Add order items
                    foreach ($orderItems as $item) {
                        if (isset($item['product_id']) && isset($item['quantity'])) {
                            $this->dataAccess->add(
                                table: 'iliad.order_items',
                                requestData: [
                                    'order_id' => $orderId,
                                    'product_id' => $item['product_id'],
                                    'quantity' => $item['quantity']
                                ]
                            );
                        }
                    }
                }

                if ($orderId) {
                    $result->setSuccessResult([
                        'order_id' => $orderId,
                        'message' => 'Order created successfully'
                    ]);
                } else {
                    $result->setError('Failed to create order', 500);
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

    public function deleteOrder($request, $response, $args)
    {
        $result = new Result();

        if ($this->validateToken($request)) {
            if (isset($args['order_id'])) {
                $orderId = $args['order_id'];

                // Delete order items first (foreign key constraint)
                $this->dataAccess->delete(
                    table: 'iliad.order_items',
                    args: ['order_id' => $orderId]
                );

                // Delete the order
                $deleted = $this->dataAccess->delete(
                    table: 'iliad.orders',
                    args: ['order_id' => $orderId]
                );

                if ($deleted) {
                    $result->setSuccessResult(['message' => 'Order deleted successfully']);
                } else {
                    $result->setError('Order not found or could not be deleted', 404);
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


    public function updateOrder($request, $response, $args)
    {
        $result = new Result();
        $requestBody = $request->getParsedBody();

        if ($this->validateToken($request)) {
            if (isset($args['order_id'])) {
                $orderId = $args['order_id'];
                $updateData = [];

                // Update order metadata (name and description)
                if (isset($requestBody['name'])) {
                    $updateData['name'] = $requestBody['name'];
                }
                if (isset($requestBody['description'])) {
                    $updateData['description'] = $requestBody['description'];
                }

                if (!empty($updateData)) {
                    $updated = $this->dataAccess->update(
                        table: 'iliad.orders',
                        args: ['order_id' => $orderId],
                        requestData: $updateData
                    );

                    if (!$updated) {
                        $result->setError('Order not found or could not be updated', 404);
                        $response->getBody()->write(json_encode($result->data));
                        return $response
                            ->withStatus($result->statusCode)
                            ->withHeader('Content-Type', 'application/json');
                    }
                }

                // Handle order items update if provided
                if (isset($requestBody['order_items'])) {
                    $orderItems = $requestBody['order_items'];

                    // Delete all existing order items for this order
                    $this->dataAccess->delete(
                        table: 'iliad.order_items',
                        args: ['order_id' => $orderId]
                    );

                    // Add new order items
                    foreach ($orderItems as $item) {
                        if (isset($item['product_id']) && isset($item['quantity']) && $item['quantity'] > 0) {
                            $this->dataAccess->add(
                                table: 'iliad.order_items',
                                requestData: [
                                    'order_id' => $orderId,
                                    'product_id' => $item['product_id'],
                                    'quantity' => $item['quantity']
                                ]
                            );
                        }
                    }

                    // Recalculate order value based on new items
                    $this->recalculateOrderValue($orderId);
                }

                $result->setSuccessResult(['message' => 'Order updated successfully']);
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

    private function recalculateOrderValue($orderId)
    {
        // Get all order items with product prices
        $orderItems = $this->dataAccess->get(
            table: "iliad.order_items",
            join: ["iliad.products" => "product_id"],
            args: ["iliad.order_items.order_id" => $orderId],
            fields: ["iliad.order_items.quantity", "iliad.products.price"]
        );

        $totalValue = 0;
        foreach ($orderItems as $item) {
            $totalValue += $item['quantity'] * $item['price'];
        }

        // Update the order with the new value
        $this->dataAccess->update(
            table: 'iliad.orders',
            args: ['order_id' => $orderId],
            requestData: ['value' => $totalValue]
        );
    }
}