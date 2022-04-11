<?php

namespace Andruby\HomeConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeShelf extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => "Y-m-d H:i:s",
        'updated_at' => "Y-m-d H:i:s",
    ];

    const TYPE = [
        'image' => '轮播图',
        'min_image' => '小图展示',
        'list' => '列表展示',
        'square' => '方格展示',
        'max_image' => '大图展示',
        'word' => '文字',
    ];

}
