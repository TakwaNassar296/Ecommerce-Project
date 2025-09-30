<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(10);

        if($categories->isEmpty())
        {
            return $this->sendResponse('No categories found' , null , 404);
        }

        $data = [
            'total' => $categories->total(),
            'per_page' => $categories->perPage(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'products' =>CategoryResource::collection($categories),
        ];

        return $this->sendResponse(
            'Categories retrieved successfully' ,$data , 200);
    }


    public function show($slug)
    {
        $category = Category::where('slug' , $slug)->first();

        if(!$category)
        {
           return $this->sendResponse('Category not found' , null ,404);
        }

       /* if(! in_array($slug , $product->getTranslations('slug')))
        {
            abort(404,'Slug not found');
        }*/

        return $this->sendResponse('Category retrieved successfully' , new CategoryResource($category) , 200);

    }
}

