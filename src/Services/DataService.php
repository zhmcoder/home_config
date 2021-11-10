<?php


namespace Andruby\HomeConfig\Services;


use Andruby\HomeConfig\Models\HomeJump;
use Illuminate\Support\Facades\Cache;

/**
 * @method static DataService instance()
 *
 * Class ChargeService
 * @package App\Api\Services
 */
class DataService
{
    public static function __callStatic($method, $params): DataService
    {
        return new self();
    }

    public function homeJump($jump_id)
    {
        $cache_key = 'home_jump_id_' . $jump_id;
        $jump_info = Cache::get($cache_key);
        if ($jump_info == null) {
            $jump_info = HomeJump::find($jump_id);
            Cache::set($cache_key, $jump_info, 60 * 10);
        }
        return $jump_info;
    }
}
