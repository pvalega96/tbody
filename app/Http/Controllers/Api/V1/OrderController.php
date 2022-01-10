<?php

namespace App\Http\Controllers\API\V1;

use App\Cart;
use App\CartProduct;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderProduct;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware('checkadmin:auth:api', ['except' => ['store']]);

    }
    public function index()
    {
        $order = Order::join('oder_product','order.id','oder_product.order_id')->get();

        $total = Order::join('oder_product','order.id','oder_product.order_id')->sum('oder_product.price');

        return response()->json(['orders' => $order, 'total' => $total], 201);

    }

    public function store(Request $request)
    {
        //Validar que tenga algo en carrito
        if($this->searchCart($request->user()->id)==true){
            return response()->json(['err' => 'Cart not found'],404); //devuelvo un resultado de exito
        }
        $order = new Order();
        $order->users_id = $request->user()->id;

        if ($order->save()) {

            $cart = Cart::join('cart_product','cart.id','cart_product.cart_id')
                ->where('cart.users_id',$request->user()->id)
                ->select(
                    'cart_product.cart_id as cart_id',
                    'product_id as product_id',
                    'cart_product.quantity as quantity',
                    'cart_product.discount as discount',
                    'cart_product.price as price'
                )
                ->get();
            foreach ($cart as $valor){
                $orderp = new OrderProduct();
                $orderp->order_id = $order->id;
                $orderp->product_id = $valor->product_id;
                $orderp->quantity = $valor->quantity;
                $orderp->discount = $valor->discount;
                $orderp->price = $valor->price;
                $orderp->save();
            }
            if ($orderp->save()) {
                $total = Cart::join('cart_product','cart.id','cart_product.cart_id')
                    ->where('cart.users_id',$request->user()->id)->sum('price');
                Order::find($order->id)
                    ->update(['total' => $total]);
                //Eliminar cart de ese usuario
                $products = $this->cart($request->user()->id);
                $this->deleteCart($cart[0]->cart_id);

                return response()->json(['message' => 'Cart create succesfully','Products' => $products, 'total'=> $total], 201);
            }else{
                return response()->json(['message' => 'Error to create Cart'], 500);
            }

        }
    }

    public function deleteCart($id){
        CartProduct::where('cart_id',$id)->delete();
        Cart::where('id',$id)->delete();
    }

    public function searchCart($id_user){
        $total = Cart::where('cart.users_id',$id_user)->first();
        if(!$total) return true;
    }

    public function cart($id_user){
        $cart = Cart::join('cart_product','cart.id','cart_product.cart_id')
            ->join('product','cart_product.product_id','product.id')
            ->where('cart.users_id',$id_user)
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

        return $cart;
    }

    public function orderDate(Request $request){
        $from = date($request->since);
        $to = date($request->until);

        $order = Order::join('oder_product','order.id','oder_product.order_id')
            ->whereBetween('order.created_at', [$from, $to])->get();

        $total = Order::join('oder_product','order.id','oder_product.order_id')
            ->whereBetween('order.created_at', [$from, $to])->sum('oder_product.price');

        return response()->json(['orders' => $order, 'total' => $total], 201);

    }


}
