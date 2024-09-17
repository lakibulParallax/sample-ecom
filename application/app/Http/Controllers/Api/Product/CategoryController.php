<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
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
        $category = Category::find($id);

        if($category){
            // Update the category
            $category->update([
                'name' => $request->name,
            ]);

            // Return success response
            return response()->json([
                'message' => 'Category updated successfully',
                'category' => $category,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
    }

    public function showCategory($id)
    {
        // Find the category by ID
        $category = Category::with('sub_categories')->find($id);

        if($category){
            // Return success response with the category data
            return response()->json([
                'category' => $category,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
    }

    public function destroyCategory($id)
    {
        // Find the category by ID
        $category = Category::find($id);

        if($category){
            // Delete the category
            $category->delete();

            // Return success response
            return response()->json([
                'message' => 'Category deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }
    }

    public function subCategoryList()
    {
        $sub_categories = SubCategory::with('category')->where('status',1)->get();
        $response = [
            'data' => $sub_categories
        ];
        return $this->successApiResponse($response);
    }

    public function storeSubCategory(Request $request)
    {
        // Validate the request data
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name',
        ]);

        $existCategory = Category::find($request->category_id);
        if($existCategory){
            // Create the category
            $sub_category = SubCategory::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
            ]);

            // Return success response
            return response()->json([
                'message' => 'Sub Category created successfully',
                'sub category' => $sub_category,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid category'
            ], 404);
        }
    }

    public function updateSubCategory(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:sub_categories,name,' . $id,
        ]);

        // Find the category by ID
        $existCategory = Category::find($request->category_id);
        if($existCategory) {
            $sub_category = SubCategory::find($id);
            if($sub_category){
                // Update the category
                $sub_category->update([
                    'category_id' => $request->category_id,
                    'name' => $request->name,
                ]);

                // Return success response
                return response()->json([
                    'message' => 'Sub Category updated successfully',
                    'sub category' => $sub_category,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'sub category not found'
                ], 404);
            }
        } else {
            return response()->json([
                'message' => 'category not found'
            ], 404);
        }
    }

    public function showSubCategory($id)
    {
        // Find the category by ID
        $sub_category = SubCategory::with('category')->find($id);

        if ($sub_category){
            // Return success response with the category data
            return response()->json([
                'sub_category' => $sub_category,
            ], 200);
        } else {
            return response()->json([
                'message' => 'sub category not found'
            ], 404);
        }
    }

    public function destroySubCategory($id)
    {
        // Find the category by ID
        $sub_category = SubCategory::find($id);

        if($sub_category){
            // Delete the sub category
            $sub_category->delete();

            // Return success response
            return response()->json([
                'message' => 'Sub Category deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'sub category not found'
            ], 404);
        }
    }

}
