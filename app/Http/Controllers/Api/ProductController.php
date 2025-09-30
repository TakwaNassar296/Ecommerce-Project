<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ProductResource;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);

        if($products->isEmpty())
        {
            return $this->sendResponse('No products found' , null , 404);
        }

        $data = [
            'total' => $products->total(),
            'per_page' => $products->perPage(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'products' =>ProductResource::collection($products),
        ];

        return $this->sendResponse('Products retrieved successfully' ,$data , 200);
    }


    public function show($slug)
    {
        $product = Product::where('slug' , $slug)->first();

        if(!$product)
        {
           return $this->sendResponse('Product not found' , null ,404);
        }

       /* if(! in_array($slug , $product->getTranslations('slug')))
        {
            abort(404,'Slug not found');
        }*/

        return $this->sendResponse('product retrieved successfully' , new ProductResource($product) , 200);

    }
}
