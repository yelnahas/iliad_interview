<?php

namespace App\Http\Controllers;

use App\Contracts\OrderInterface;
use App\Contracts\ApiResponseInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    protected $apiResponse;

    public function __construct(OrderInterface $orderService, ApiResponseInterface $apiResponse)
    {
        $this->orderService = $orderService;
        $this->apiResponse = $apiResponse;
    }

    /**
     * @OA\Get(
     *     path="/api/orders/search",
     *     summary="Search for orders",
     *     description="This endpoint allows searching for orders based on the provided data. The 'date' field is required, while 'name' and 'description' are optional.",
     *     security={{"bearerAuth":{}}},
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=true,
     *         description="The date of the order in the format YYYY-MM-DD",
     *         @OA\Schema(type="string", example="2025-01-01")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         required=false,
     *         description="The name of the order",
     *         @OA\Schema(type="string", example="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         required=false,
     *         description="A brief description of the order",
     *         @OA\Schema(type="string", example="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order viewed successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="string"),
     *                     @OA\Property(property="description", type="string", example="string"),
     *                     @OA\Property(property="date", type="string", example="2024-01-01"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                     @OA\Property(
     *                         property="products",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="product_name"),
     *                             @OA\Property(property="price", type="number", format="float", example=99.99),
     *                             @OA\Property(property="stock", type="integer", example=10),
     *                             @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                             @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                             @OA\Property(
     *                                 property="pivot",
     *                                 type="object",
     *                                 @OA\Property(property="order_id", type="integer", example=2),
     *                                 @OA\Property(property="product_id", type="integer", example=1),
     *                                 @OA\Property(property="quantity", type="integer", example=10),
     *                                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z")
     *                             )
     *                         )
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 nullable=true,
     *                 example=null
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The date field is required."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order not found."
     *             )
     *         )
     *     )
     * )
     */

    public function search(Request $request): JsonResponse
    {
        try { 
            $data = $request->all();
            $order = $this->orderService->searchOrder($data);

            return $this->apiResponse->success($order, "Order viewed successfully.");
        }
        catch (\Exception $e) {
            return $this->apiResponse->error($e->getMessage(), [], $e->getCode() === 0 ? 400 : $e->getCode());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/orders",
     *     summary="Create a new order",
     *     description="This endpoint creates a new order with the provided data.",
     *     security={{"bearerAuth":{}}},
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "description", "products"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="The name of the order"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="A brief description of the order"
     *             ),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 description="List of products included in the order",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id", "quantity"},
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         description="The ID of the product",
     *                         example=1
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         description="The quantity of the product",
     *                         example=10
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="success"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order created successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="name", type="string", example="string"),
     *                 @OA\Property(property="description", type="string", example="string"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00.000000Z"),
     *                 @OA\Property(property="id", type="integer", example=2)
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 nullable=true,
     *                 example=null
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input data"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="array",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Product not found."
     *             )
     *         )
     *     )
     * )
     */

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $order = $this->orderService->createOrder($data);

            return $this->apiResponse->success($order, "Order created successfully.", 201);
        }
        catch (\Exception $e) {
            return $this->apiResponse->error($e->getMessage(), [], $e->getCode() === 0 ? 400 : $e->getCode());
        }
     
    }


    /**
     * @OA\Put(
     *     path="/api/orders",
     *     summary="Update an order",
     *     description="Updates the details of an order based on the provided data.",
     *     security={{"bearerAuth":{}}},
     *     tags={"Orders"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 description="ID of the order to update",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="Name associated with the order",
     *                 example="string"
     *             ),
     *             @OA\Property(
     *                 property="description",
     *                 type="string",
     *                 description="Description of the order",
     *                 example="string"
     *             ),
     *             @OA\Property(
     *                 property="products",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="integer",
     *                         description="ID of the product",
     *                         example=7
     *                     ),
     *                     @OA\Property(
     *                         property="quantity",
     *                         type="integer",
     *                         description="Quantity of the product",
     *                         example=5
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order updated successfully."
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Updated order details"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Invalid input data"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order not found."
     *             )
     *         )
     *     )
     * )
     */

    public function update(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            $order = $this->orderService->updateOrder($data);
            
            return $this->apiResponse->success($order, "Order updated successfully.");
        }
        catch (\Exception $e) {
            return $this->apiResponse->error($e->getMessage(), [], $e->getCode() === 0 ? 400 : $e->getCode());
        }
    
    }


    /**
     * @OA\Delete(
     *     path="/api/orders/{id}",
     *     summary="Delete an order",
     *     description="This endpoint deletes the specified order based on the provided ID.",
     *     security={{"bearerAuth":{}}},
     *     tags={"Orders"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the order to be deleted",
     *         @OA\Schema(
     *             type="string",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order deleted successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 example="error"
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="success",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Order not found."
     *             )
     *         )
     *     )
     * )
     */

    public function destroy(string $id): JsonResponse
    {
        try {

            $data = $id;
            $this->orderService->deleteOrder($data);

            return $this->apiResponse->success([], "Order deleted successfully.");
        }
        catch (\Exception $e) {
            return $this->apiResponse->error($e->getMessage(), [], $e->getCode() === 0 ? 400 : $e->getCode());
        }
    }
}


?>