<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    protected $table = 'cart_product';
    protected $fillable = [
        'id','cart_id','product_id','quantity','discount','price'
    ];
    public $timestamps = false;
}
