<?php

namespace App\Http\Controllers\API\V1;

use App\Cart;
use App\CartProduct;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CartRequest;
use App\Http\Requests\V1\EditCartRequest;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CartController extends Controller
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
    public function index(Request $request)
    {
        $cart = Cart::join('cart_product','cart.id','cart_product.cart_id')
            ->join('product','cart_product.product_id','product.id')
            ->where('cart.users_id',$request->user()->id)
            ->select(
                'cart_product.id as cartproduct_id',
                'cart.id as cart_id',
                'product.id as product_id',
                'product.name as product_name',
                'product.price as product_price',
                'cart_product.discount as product_discount',
                'cart_product.quantity as quantity',
                'cart_product.price as productcart_price'
            )
            ->get();

        $total = Cart::join('cart_product','cart.id','cart_product.cart_id')
            ->where('cart.users_id',$request->user()->id)->sum('price');

        return response()->json(['cart' => $cart, 'total' => $total], 201);



    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CartRequest $request)
    {
        $product = Stock::join('product','stock.product_id','product.id')->where('product.id',$request->input('product_id'))->first();
        if($product){
            if($product->quantity >= $request->input('quantity')) {

                $pricediscount = $product->price - ($product->price * ($request->input('discount') / 100));

                $cart = Cart::firstOrCreate(
                    ['users_id' =>  $request->user()->id]
                );
                if ($cart->save()) {
                    $cartp = new CartProduct();
                    $cartp->cart_id = $cart->id;
                    $cartp->product_id = $request->input('product_id');
                    $cartp->quantity = $request->input('quantity');
                    $cartp->discount = $request->input('discount');
                    $cartp->price = $pricediscount*$request->input('quantity');
                    if ($cartp->save()) {

                        Stock::where('product_id',$request->input('product_id'))
                            ->decrement("quantity", $request->input('quantity'));

                        return response()->json(['message' => 'Product add succesfully', 'product' => $cartp], 201);
                    } else {
                        return response()->json(['message' => 'Error to add Product'], 500);
                    }
                }
                return response()->json(['message' => 'Error to create Product'], 500);
            }else{
                return response()->json(['message' => 'Out of stock'], 500);
            }
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
    public function update(Request $request, $id)
    {
        //operation 1 sum y 0 resta
        $cartp = CartProduct::where('id',$id)->first();
        if($cartp){
            $product = Stock::join('product','stock.product_id','product.id')->where('product.id',$cartp->product_id)->first();
            if($request->input('operation')==1){
               //suma 1
               if($product->quantity >= 1) {

                   $pricediscount = $product->price - ($product->price * ($cartp->discount / 100));
                   $cartp1 = CartProduct::where('id', $id)
                       ->update([
                           'quantity'=> DB::raw('quantity+1'),
                           'price' => $pricediscount*($cartp->quantity+1)
                   ]);
                   if($cartp1){
                       Stock::where('product_id',$cartp->product_id)
                           ->decrement("quantity", 1);
                       return response()->json(['err' => 'ProductCart Add'],200); //devuelvo un resultado de exito
                   }else{
                       return response()->json(['err' => 'Error ProductCart'],404); //devuelvo un resultado de exito

                   }

                }else{
                   return response()->json(['message' => 'Out of stock'], 500);
               }

           }elseif($request->input('operation')==0){
                //si cantidad es cero eliminarlo
               //resta 1
               $pricediscount = $product->price - ($product->price * ($cartp->discount / 100));
                $cartp1 = CartProduct::where('id', $id)
                    ->update([
                        'quantity'=> DB::raw('quantity-1'),
                        'price' => $pricediscount*($cartp->quantity-1)
                    ]);
                if($cartp1){
                    Stock::where('product_id',$cartp->product_id)
                        ->increment("quantity", 1);
                    if($cartp->quantity==1){
                        $this->destroy($cartp->id);
                    }
                    return response()->json(['err' => 'ProductCart Deleted'],200); //devuelvo un resultado de exito
                }else{
                    return response()->json(['err' => 'Error ProductCart'],404); //devuelvo un resultado de exito

                }

           }
        }else{
            return response()->json(['err' => 'ProductCart not found'],404); //devuelvo un resultado de exito
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
        $cartp = CartProduct::find($id);
        if($cartp==null){

            return response()->json(['err' => 'Product from cart not found'],404); //devuelvo un resultado de exito

        }
        if($cartp->delete()){
            Stock::where('product_id',$cartp->product_id)
                ->increment("quantity", $cartp->quantity);
            return response()->json(['res' => 'Deleted Product from cart'],202); //devuelvo un resultado de exito
        }
    }

}
