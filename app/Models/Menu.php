<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = [];

    // 메뉴는 하나의 카테고리에 속함
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
