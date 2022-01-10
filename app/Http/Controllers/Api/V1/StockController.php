<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EditStockRequest;
use App\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('checkadmin:auth:api', ['except' => ['index', 'show']]);

    }

    public function index()
    {
        return Stock::join('product','stock.product_id','product.id')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Stock::join('product','stock.product_id','product.id')->where('product.id',$id)->first();
        if($product){
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
    public function update(EditStockRequest $request, $id)
    {
        $product=Stock::where('product_id',$id)->first();
        if($product){
            $product->update($request->all());
            return response()->json(['res' => $product],202); //devuelvo un resultado de exito
        }else{
            return response()->json(['err' => 'Product not found'],404); //devuelvo un resultado de exito
        }
    }


}
