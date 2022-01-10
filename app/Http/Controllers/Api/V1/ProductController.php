<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EditProductRequest;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Requests\V1\ProductUploadRequest;
use App\Product;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = new Product();
        $product->name = $request->input('name');
        $product->price = $request->input('price');
        $product->status = 1;

        if ($product->save()) {
            $stock = new Stock();
            $stock->product_id = $product->id;
            $stock->quantity = 0;
            if ($stock->save()) {
                return response()->json(['message' => 'Product create succesfully'], 201);
            }else{
                return response()->json(['message' => 'Error to create Product'], 500);
            }
        }
        return response()->json(['message' => 'Error to create Product'], 500);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if($product){
            return response()->json(['res' => $product],202); //devuelvo un resultado de exito
        }else{
            return response()->json(['err' => 'Product not found'],404); //devuelvo un resultado de exito
        }
    }

    public function search($name)
    {
        $product = Product::where('name','like','%'.$name.'%')->get();
        if(strlen($product)>2){
            return response()->json(['res' => $product],202); //devuelvo un resultado de exito
        }else{
            return response()->json(['err' => 'Product not found'],404); //devuelvo un resultado de exito
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(EditProductRequest $request, $id)
    {
        $product = Product::where('id',$id)->first();
        if($product){
            $product->update($request->all());
            return response()->json(['res' => $product],200); //devuelvo un resultado de exito
        }else{
            return response()->json(['err' => 'Product not found'],404); //devuelvo un resultado de exito
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::where('id',$id);
        if($product==null){
            return response()->json(['err' => 'Product not found'],404); //devuelvo un resultado de exito
        }
        if($product->delete()){
            return response()->json(['res' => 'Deleted Product'],202); //devuelvo un resultado de exito
        }else{
            return response()->json(['err' => 'Product not found'],404); //devuelvo un resultado de exito
        }
    }


    

}
