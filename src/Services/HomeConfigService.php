<?php


namespace Andruby\HomeConfig\Services;


use Andruby\DeepAdmin\Models\Content;
use Andruby\HomeConfig\Models\HomeConfig;
use Andruby\HomeConfig\Models\HomeConfigId;
use Andruby\HomeConfig\Models\HomeItem;
use Andruby\HomeConfig\Models\HomeJump;

class  HomeConfigService
{
    public function home_data($app_id, $os_type, $date_time)
    {
        if ($date_time == null) {
            $date_time = date('Y-m-d H:i:s');
        }
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
            ->with('homeShelf')
            ->orderByDesc('sort')->orderByDesc('id')
            ->get()->toArray();

        foreach ($homeConfigList as &$config_data) {
            $config_ids = HomeConfigId::where('config_id', $config_data['id'])->orderByDesc('sort')->orderByDesc('id')->get()->toArray();
            $config_items = [];
            $configIdsCount = count($config_ids);
            $showCount = $configIdsCount > $config_data['show_num'] ? $config_data['show_num'] : $configIdsCount;
            for ($i = 0; $i < $showCount; $i++) {
                $config = $config_ids[$i];

                $config['image'] = http_path($config['image']);
                if ($config['data_type'] == HomeJump::DATA_TYPE_TABLE) {
                    $jump_info = DataService::instance()->homeJump($config['jump_id']);
                    $table_info = table_info($jump_info['table_info']);
                    if (method_exists($this, $table_info['table_name'])) {
                        $fun_name = $table_info['table_name'];
                        $item_data = $this->$fun_name($table_info, $config, $config_data);
                        if (!empty($item_data)) {
                            $config_items[] = $item_data;
                        }
                    } else {
                        $config_items[] = $config;
                    }
                } else {
                    if (method_exists($this, 'home_item')) {
                        $config = $this->home_item($config);
                    }
                    $config_items[] = $config;
                }
            }
            $config_data['items'] = $config_items;
            $config_data['image'] = http_path($config_data['image']);
            $config_data['shelf_type'] = $config_data['home_shelf']['type'];

            unset($config_data['shelf_on']);
            unset($config_data['sort']);
            unset($config_data['column_count']);
            unset($config_data['publish_up']);
            unset($config_data['publish_down']);
            unset($config_data['show_app']);
            unset($config_data['home_shelf']);
        }

        return $homeConfigList;
    }

    protected function home_item($config)
    {
        $homeItem = HomeItem::query()->find($config['third_id']);

        // todo 是否是链接
        $content = $homeItem['content'] ?? '';
        if (substr($content, 0, 4) == 'http') {
            $config['content'] = $content;
            $config['jump_type'] = 'link';
        } else {
            $config['jump_type'] = 'text';
        }

        return $config;
    }
}
