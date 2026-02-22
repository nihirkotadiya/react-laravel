<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * List products with search, filter, sort, and pagination.
     * GET /api/products?search=&category_id=&sort_price=asc|desc&page=
     */
    public function index(Request $request)
    {
        try {
            $query = Product::with('category'); // eager load category name

            // Search by product name
            if ($request->has('search') && $request->search !== '') {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by category
            if ($request->has('category_id') && $request->category_id !== '') {
                $query->where('category_id', $request->category_id);
            }

            // Sort by price (asc or desc)
            if ($request->has('sort_price') && in_array($request->sort_price, ['asc', 'desc'])) {
                $query->orderBy('price', $request->sort_price);
            } else {
                $query->latest(); // default: newest first
            }

            // Paginate 10 per page
            $products = $query->paginate(10);

            return response()->json([
                'success' => true,
                'data'    => $products,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products.',
            ], 500);
        }
    }

    /**
     * Get a single product.
     * GET /api/products/{id}
     */
    public function show(Product $product)
    {
        $product->load('category');
        return response()->json([
            'success' => true,
            'data'    => $product,
        ]);
    }

    /**
     * Create a new product.
     * POST /api/products
     */
    public function store(Request $request)
    {
        try {
            // Validate all product fields
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
                'description' => 'nullable|string',
                'status'      => 'required|in:active,inactive',
            ]);

            $product = Product::create($validated);
            $product->load('category');

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'data'    => $product,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product.',
            ], 500);
        }
    }

    /**
     * Update an existing product.
     * PUT /api/products/{id}
     */
    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'category_id' => 'required|exists:categories,id',
                'price'       => 'required|numeric|min:0',
                'stock'       => 'required|integer|min:0',
                'description' => 'nullable|string',
                'status'      => 'required|in:active,inactive',
            ]);

            $product->update($validated);
            $product->load('category');

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'data'    => $product,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product.',
            ], 500);
        }
    }

    /**
     * Delete a product.
     * DELETE /api/products/{id}
     */
    public function destroy(Product $product)
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete product.',
            ], 500);
        }
    }
}
