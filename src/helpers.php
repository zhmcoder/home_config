<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('table_info')) {

    function table_info($table_info)
    {
        $table_info = explode("\n", $table_info);
        $fields = explode(",", $table_info[1]);
        return ['table_name' => $table_info[0], 'fields' => $fields];
    }
}