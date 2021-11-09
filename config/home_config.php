<?php

return [

    /*
    |--------------------------------------------------------------------------
    | The model you want to use as the user model
    |
    */

    'model' => 'App\UcenterMember',

    /*
    |--------------------------------------------------------------------------
    | The amount of active tokens a user can have.
    | The amount of active tokens is unlimited when the value is null
    |
    */

    'total_active_tokens' => null,

    'multi_login' => false,

    'token_expire_time' => 3 * 30 * 24 * 60 * 60,

    // 数据类型
    'data_type' => [
        '1' => '自有录入数据',
        '2' => '表关联数据'
    ],

    // 表单类型
    'form_type' => [
        '1' => [
            'input' => '输入框',
            'textArea' => '长文本（textarea）',
            'wangEditor' => '富文本编辑器',
        ],
        '2' => [
            'selectTable' => '下拉单选（连表查询）',
        ],
    ],

];
