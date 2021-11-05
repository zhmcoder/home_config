<?php

namespace Andruby\HomeConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeConfigType extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'created_at' => "Y-m-d H:i:s",
        'updated_at' => "Y-m-d H:i:s",
    ];

    const CONFIG_TYPE_HOME_STYLE = 1;//首页样式
    const CONFIG_TYPE_ITEM_JUMP = 2;//跳转类型


}
