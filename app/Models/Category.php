<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = []; // 대량 할당 허용

    // 카테고리는 여러 메뉴를 가질 수 있음 (1:N 관계)
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}
