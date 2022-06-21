<?php

namespace Andruby\HomeConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeItem extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => "Y-m-d H:i:s",
        'updated_at' => "Y-m-d H:i:s",
        'show_app' => 'array',
    ];

    const CONFIG_TYPE = [
        '1' => '轮播图',
        '2' => '金刚圈',
    ];
}
