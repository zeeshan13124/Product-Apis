<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      title="Products APIs",
 *      version="1.0.0",
 *      description="API documentation for managing products.",
 *      @OA\Contact(
 *          name="Your Name",
 *          email="your.email@example.com"
 *      ),
 * )
 */


class ProductController extends Controller
{


    /**
     * @OA\Get(
     *     path="/api/products/getProducts",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by product name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by product category",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Minimum product price",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Maximum product price",
     *         @OA\Schema(type="number")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by field (name or price)",
     *         @OA\Schema(type="string", enum={"name", "price"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_dir",
     *         in="query",
     *         description="Sort direction (asc or desc)",
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of products",

     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example={"The name field must be a string."}),
     *         ),
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by name
        if ($request->has('name')) {
            $query->where('name', $request->input('name'));
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by price range (min and max)
        if ($request->has(['min_price', 'max_price'])) {
            $query->whereBetween('price', [$request->input('min_price'), $request->input('max_price')]);
        }

        // Sort by name or price
        if ($request->has('sort_by')) {
            $sortField = $request->input('sort_by');
            $sortDirection = $request->input('sort_dir', 'asc');
            $query->orderBy($sortField, $sortDirection);
        }

        $products = $query->get(['name', 'price', 'category']);

        return response()->json($products, 200);
    }
/**
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="category", type="string"),
 * )
 */
    /**
     * @OA\Get(
     *     path="/api/products/showProduct",
     *     summary="Get a specific product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="ID of the product to fetch",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",

     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example={"The id field is required."}),
     *         ),
     *     ),
     * )
     */
    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $product = Product::find($request->input('id'), ['name', 'price', 'category']);

        return response()->json($product, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products/saveProducts",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "price", "category"},
     *                 @OA\Property(property="name", type="string", example="Product Name"),
     *                 @OA\Property(property="price", type="number", format="float", example=19.99),
     *                 @OA\Property(property="category", type="string", example="Electronics"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",

     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example={"The name field is required."}),
     *         ),
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->category = $request->input('category');
        $product->save();

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/products/updateProduct",
     *     summary="Update a product by ID",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Updated Product Name"),
     *                 @OA\Property(property="price", type="number", format="float", example=29.99),
     *                 @OA\Property(property="category", type="string", example="Updated Category"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example={"The id field is required."}),
     *         ),
     *     ),
     * )
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $product = Product::find($request->input('id'));

        if (!$product) {
            return response(['message' => 'Product not found'], 404);
        }

        // Update name if present in the request
        if ($request->has('name')) {
            $product->name = $request->input('name');
        }

        // Update price if present in the request
        if ($request->has('price')) {
            $product->price = $request->input('price');
        }

        // Update category if present in the request
        if ($request->has('category')) {
            $product->category = $request->input('category');
        }

        // Save the changes
        $product->save();

        // Fetch the updated product from the database
        $updatedProduct = Product::find($product->id);

        return response()->json(['message' => 'Product updated successfully', 'product' => $updatedProduct], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/deleteProduct",
     *     summary="Delete a product by ID",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"id"},
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="ID of the product to delete"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example={"The id field is required."}),
     *         ),
     *     ),
     * )
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $product = Product::find($request->input('id'));

        if (!$product) {
            return response(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
