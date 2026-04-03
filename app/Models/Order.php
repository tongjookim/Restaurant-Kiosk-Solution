<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    // 주문은 여러 개의 주문 상세(메뉴)를 가짐
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
