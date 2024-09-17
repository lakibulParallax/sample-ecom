<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ApiResponseTrait;

    public function list()
    {
        $items = Brand::where('status',1)->get();
        $response = [
            'data' => $items
        ];
        return $this->successApiResponse($response);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
        ]);

        // Create the Brand
        $brand = Brand::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
        ]);

        // Return success response
        return response()->json([
            'message' => 'Brand created successfully',
            'brand' => $brand,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        // Find the Brand by ID
        $brand = Brand::find($id);

        if($brand){
            // Update the Brand
            $brand->update([
                'name' => $request->name,
                'company_name' => $request->company_name,
            ]);

            // Return success response
            return response()->json([
                'message' => 'Brand updated successfully',
                'brand' => $brand,
            ], 200);
        } else {
            return response()->json([
                'message' => 'brand not found',
            ], 404);
        }
    }

    public function show($id)
    {
        // Find the Brand by ID
        $brand = Brand::find($id);

        if($brand){
            return response()->json([
                'brand' => $brand,
            ], 200);
        } else {
            return response()->json([
                'message' => 'brand not found',
            ], 404);
        }

    }

    public function destroy($id)
    {
        // Find the Brand by ID
        $brand = Brand::find($id);

        if($brand){
            // Delete the Brand
            $brand->delete();

            // Return success response
            return response()->json([
                'message' => 'Brand deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'brand not found',
            ], 404);
        }
    }
}
