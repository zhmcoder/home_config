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
            ->orderBy('sort', 'asc')->orderBy('id', 'desc')->get()->toArray();

        foreach ($homeConfigList as &$config_data) {

            $config_ids = HomeConfigId::where('config_id', $config_data['id'])->get()->toArray();
            $config_items = [];
            foreach ($config_ids as $config) {
                $config_items[] = $this->getJumpInfo($config['third_id'], $config['jump_id']);
            }
            $config_data['items'] = $config_items;

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

    private function getJumpInfo($third_id, $jump_id)
    {
        $jump_info = HomeJump::find($jump_id);
        $item_data = null;
        if ($jump_info) {
            if ($jump_info['data_type'] == HomeJump::DATA_TYPE_INPUT) {
                $item_data = HomeItem::query()->select(['id', 'name', 'image', 'content'])->findOrFail($third_id);
                if ($item_data) {
                    $item_data = $item_data->toArray();
                    $item_data['image'] = http_path($item_data['image']);
                }
            } else {
                $table_info = table_info($jump_info['table_info']);
                if (method_exists($this, $table_info['table_name'])) {
                    $fun_name = $table_info['table_name'];
                    $item_data = $this->$fun_name($table_info['table_name'], $third_id);
                } else {
                    $model = new Content($table_info['table_name']);
                    $item_data = $model->select($table_info['fields'])->findOrFail($third_id);
                    if ($item_data) {
                        $item_data = $item_data->toArray();
                    }
                }
            }
        }
        return $item_data;
    }

    protected function goods($table_name, $id)
    {
        return ['table_name' => $table_name, 'id' => $id];
    }

}
