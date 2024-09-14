<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function categoryList()
    {
        $categories = Category::with('sub_categories')->where('status',1)->get();
        $response = [
            'data' => $categories
        ];
        return $this->successApiResponse($response);
    }

    public function storeCategory(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // Create the category
        $category = Category::create([
            'name' => $request->name,
        ]);

        // Return success response
        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ], 200);
    }

    public function updateCategory(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        // Find the category by ID
        $category = Category::findOrFail($id);

        // Update the category
        $category->update([
            'name' => $request->name,
        ]);

        // Return success response
        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category,
        ], 200);
    }

    public function show($id)
    {
        // Find the category by ID
        $category = Category::findOrFail($id);

        // Return success response with the category data
        return response()->json([
            'category' => $category,
        ], 200);
    }

    public function destroy($id)
    {
        // Find the category by ID
        $category = Category::findOrFail($id);

        // Delete the category
        $category->delete();

        // Return success response
        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }

}
