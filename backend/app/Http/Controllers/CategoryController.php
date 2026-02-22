<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * List all categories with pagination, or all if requested.
     * GET /api/categories
     */
    public function index(Request $request)
    {
        try {
            // If requested, return all categories without pagination (useful for dropdowns)
            if ($request->has('all')) {
                $categories = Category::latest()->get();
                return response()->json([
                    'success' => true,
                    // Wrap in 'data' so the frontend map (response.data.data.data) still works smoothly
                    'data'    => [
                        'data' => $categories
                    ],
                ]);
            }

            // Otherwise, return 10 categories per page
            $categories = Category::latest()->paginate(10);

            return response()->json([
                'success' => true,
                'data'    => $categories,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch categories.',
            ], 500);
        }
    }

    /**
     * Get a single category.
     * GET /api/categories/{id}
     */
    public function show(Category $category)
    {
        return response()->json([
            'success' => true,
            'data'    => $category,
        ]);
    }

    /**
     * Create a new category.
     * POST /api/categories
     */
    public function store(Request $request)
    {
        try {
            // Validate input
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'status'      => 'required|in:active,inactive',
            ]);

            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data'    => $category,
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
                'message' => 'Failed to create category.',
            ], 500);
        }
    }

    /**
     * Update an existing category.
     * PUT /api/categories/{id}
     */
    public function update(Request $request, Category $category)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'status'      => 'required|in:active,inactive',
            ]);

            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data'    => $category,
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
                'message' => 'Failed to update category.',
            ], 500);
        }
    }

    /**
     * Delete a category.
     * DELETE /api/categories/{id}
     */
    public function destroy(Category $category)
    {
        try {
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete category.',
            ], 500);
        }
    }
}
