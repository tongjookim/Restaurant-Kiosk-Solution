<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    // 어떤 주문에 속해 있는지
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ✅ 핵심: 어떤 메뉴인지 연결 (이 부분이 있어야 이름을 가져올 수 있습니다!)
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}