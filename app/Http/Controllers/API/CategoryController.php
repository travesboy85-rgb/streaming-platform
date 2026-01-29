<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        return response()->json([
            'message' => 'Categories retrieved successfully',
            'data' => $categories
        ]);
    }

    public function show($id)
    {
        $category = Category::with(['videos' => function($query) {
            $query->latest()->limit(10);
        }])->find($id);
        
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
        
        return response()->json([
            'message' => 'Category retrieved successfully',
            'data' => $category
        ]);
    }
}