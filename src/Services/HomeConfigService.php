<?php


namespace Andruby\HomeConfig\Services;


use Andruby\HomeConfig\Models\HomeConfig;
use App\Api\Libs\WXBizDataCrypt;
use Cache;

class HomeConfigService
{
    public static function home_data($app_id, $os_type, $date_time)
    {
        $homeConfigList = HomeConfig::query()->where('shelf_on', 1)
            ->where('show_app', 'like', '%"' . $app_id . '"%')
            ->where(function ($query) use ($date_time) {
                $query->where('publish_up', '<=', $date_time)
                    ->orWhere(function ($query) {
                        $query->where('publish_up', null);
                    });
            })
            ->where(function ($query) use ($date_time) {
                $query->where('publish_down', '>=', $date_time)
                    ->orWhere(function ($query) {
                        $query->where('publish_down', null);
                    });
            })
            ->orderBy('sort', 'asc')->orderBy('id', 'desc')->get()->toArray();

        foreach ($homeConfigList as &$config_data) {
//            $config

            unset($config_data['shelf_id']);
            unset($config_data['shelf_on']);
            unset($config_data['show_num']);
            unset($config_data['sort']);
            unset($config_data['column_count']);
            unset($config_data['publish_up']);
            unset($config_data['publish_down']);
            unset($config_data['show_app']);
        }

        return $homeConfigList;
    }
}
