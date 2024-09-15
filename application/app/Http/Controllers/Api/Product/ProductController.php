<?php

namespace App\Http\Controllers\Api\Product;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\FileManager;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function list()
    {
        $items = Product::with('fileManager')->where('status',1)->latest()->paginate(10);
        $response = [
            'data' => $items
        ];
        return $this->successApiResponse($response);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'nullable|exists:sub_categories,id',
            'brand_id'         => 'required|exists:brands,id',
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'shipping_cost'    => 'nullable|numeric',
            'quantity'         => 'required|numeric',
            'sku'              => 'required|string|max:255',
        ]);

        // Create the Product
        $p = new Product();
        $p->name                = $request->name;
        $p->category_id         = $request->category_id;
        $p->sub_category_id     = $request->sub_category_id;
        $p->brand_id            = $request->brand_id;
        $p->price               = $request->price;
        $p->discount            = $request->discount ?? 0;
        $p->shipping_cost       = $request->shipping_cost ?? 0;
        $p->slug                = Helpers::generate_slug($request->name);
        $p->sku                 = $request->sku;
        $p->quantity            = $request->quantity;
        $p->color               = $request->color;
        $p->other               = $request->other;
        $p->size                = $request->size;
        $p->description         = $request->description;
        $p->creator_id          = Auth::id();
        $p->creator_type        = get_class(Auth::user());
        $p->save();
        if (isset($request->main_image)) {
            $file_manager = (new FileManager())->upload('product_image', $request->main_image);
            if ($file_manager->id != 0) {
                $file_manager->origin()->associate($p)->save();
            }
        }

        // Return success response
        return response()->json([
            'message' => 'Product created successfully',
            'product' => $p,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $request->validate([
            'category_id'      => 'required|exists:categories,id',
            'sub_category_id'  => 'nullable|exists:sub_categories,id',
            'brand_id'         => 'required|exists:brands,id',
            'name'             => 'required|string|max:255',
            'price'            => 'required|numeric',
            'discount'         => 'nullable|numeric',
            'shipping_cost'    => 'nullable|numeric',
            'quantity'         => 'required|numeric',
            'sku'              => 'required|string|max:255',
        ]);

        $product = Product::find($id);
        if($product){
            // Update the product fields
            $product->name                = $request->name;
            $product->category_id         = $request->category_id;
            $product->sub_category_id     = $request->sub_category_id;
            $product->brand_id            = $request->brand_id;
            $product->price               = $request->price;
            $product->discount            = $request->discount ?? 0;
            $product->shipping_cost       = $request->shipping_cost ?? 0;
            $product->slug                = Helpers::generate_slug($request->name);
            $product->sku                 = $request->sku;
            $product->quantity            = $request->quantity;
            $product->color               = $request->color;
            $product->other               = $request->other;
            $product->size                = $request->size;
            $product->description         = $request->description;
            $product->creator_id          = Auth::id();
            $product->creator_type        = get_class(Auth::user());

            // Save the updated product
            $product->save();

            if (isset($request->avatar)) {
                if ($product->fileManager) {
                    $product->fileManager->uploadUpdate('product_image', $request->avatar);
                } else {
                    $file_manager = (new FileManager())->upload('product_image', $request->avatar);
                    if ($file_manager->id != 0) {
                        $file_manager->origin()->associate($product)->save();
                    }
                }
            }
            // Return success response
            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid Product id'
            ], 404);
        }
    }

    public function show($id)
    {
        // Find the Product by ID
        $item = Product::with('fileManager')->find($id);

        if($item){
            return response()->json([
                'product' => $item,
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid product id',
            ], 404);
        }

    }

    public function destroy($id)
    {
        // Find the Product by ID
        $item = Product::find($id);

        if($item){
            // Delete the Product
            $item->delete();

            // Return success response
            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid Product id',
            ], 404);
        }
    }
}
