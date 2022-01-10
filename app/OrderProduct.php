<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'oder_product';
    protected $fillable = [
        'id','order_id', 'product_id', 'quantity','discount','price'
    ];
    public $timestamps = false;
}
