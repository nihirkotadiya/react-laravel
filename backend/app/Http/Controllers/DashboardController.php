<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get dashboard statistics based on user role.
     * GET /api/dashboard/stats
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $stats = [];

        // Both Admin and Manager can see product stats
        if (in_array($user->role, ['admin', 'manager'])) {
            $stats['total_products'] = Product::count();
            $stats['active_products'] = Product::where('status', 'active')->count();
        }

        // Only Admin can see category stats
        if ($user->role === 'admin') {
            $stats['total_categories'] = Category::count();
            $stats['active_categories'] = Category::where('status', 'active')->count();
        }

        return response()->json([
            'success' => true,
            'data'    => $stats,
        ]);
    }
}
