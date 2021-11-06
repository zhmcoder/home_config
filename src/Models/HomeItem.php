<?php

namespace Andruby\HomeConfig\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeItem extends Model
{
    use SoftDeletes;

    const CONFIG_TYPE = [
        '1' => '轮播图',
        '2' => '金刚圈',
    ];
}
