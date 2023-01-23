<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!Auth::hasUser())
        {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        $products = Product::where('user_id', Auth::user()->getAuthIdentifier())->paginate();
        return new ProductCollection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        if(!Auth::hasUser())
        {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        $product = new Product();
        $product->user_id = Auth::user()->getAuthIdentifier();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->quantity = $request->input('quantity');
        $product->unit_price = $request->input('unit_price');
        $product->save();

        return response()->json(new ProductResource($product), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        if(!Auth::hasUser() && Auth::user()->getAuthIdentifier() == $product->user_id)
        {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        return response()->json(new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        if(!Auth::hasUser() && Auth::user()->getAuthIdentifier() == $product->user_id)
        {
            return response()->json(["message" => "Unauthorized"], 403);
        }

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->quantity = $request->input('quantity');
        $product->unit_price = $request->input('unit_price');
        $product->save();

        return response()->json(new ProductResource($product));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if(!Auth::hasUser() && Auth::user()->getAuthIdentifier() == $product->user_id){
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $product->delete();

        return response()->json(null, 204);
    }
}
