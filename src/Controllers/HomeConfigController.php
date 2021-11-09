<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Grid\SortEdit;
use Andruby\DeepAdmin\Services\GridCacheService;
use Andruby\HomeConfig\Models\HomeConfig;
use Andruby\HomeConfig\Models\HomeJump;
use Andruby\HomeConfig\Models\HomeConfigId;
use Illuminate\Support\Facades\DB;
use SmallRuralDog\Admin\Components\Form\Input;
use SmallRuralDog\Admin\Components\Form\Radio;
use SmallRuralDog\Admin\Components\Widgets\Card;
use Andruby\DeepAdmin\Controllers\ContentController;
use SmallRuralDog\Admin\Grid;
use Andruby\DeepAdmin\Models\Content;
use SmallRuralDog\Admin\Layout\Row;

// 货架关联
class HomeConfigController extends ContentController
{
    protected function getTableName()
    {
        return 'home_configs';
    }

    protected function grid_action(Grid\Actions $actions)
    {
        $row = $actions->getRow();

//        $title = HomeConfig::SHELF_TYPE[$row['shelf_type']];
//        $title = GridCacheService::instance()->get_cache_value(HomeShelf::class,
//            'home_shelf_' . $row['shelf_id'], $row['shelf_type'], 'id', 'name');
        $actions->add(Grid\Actions\ActionButton::make('关联数据')
            ->handler(Grid\Actions\ActionButton::HANDLER_ROUTE)
            ->uri('/home/config/relation_grid/{id}' . '?timestamp=' . time()));

        $actions->setDeleteAction(new Grid\Actions\DeleteDialogAction());
    }

    public function relation_grid(\SmallRuralDog\Admin\Layout\Content $content, $home_config_id = null)
    {
        $grid_type = request('grid_type');

        $grid_left = $this->column_grid(1, $home_config_id);
        $grid_right = $this->column_grid(2, $home_config_id);

        $homeConfig = HomeConfig::query()->find($home_config_id);

        $content->row(function (Row $row) use ($grid_left, $grid_right, $homeConfig) {
            $row->gutter(0)->className('mt-10');
            $row->column(12, Card::make()->header('货架：' . $homeConfig['name'] . '，已关联内容')->bodyStyle('padding:0px;margin_left:100px;')->content(
                $grid_left
            ));
            $row->column(12, Card::make()->header('列表')->bodyStyle('padding:0px;')->content(
                $grid_right
            ));
        });
        if ($grid_type == 1) {
            return $this->isGetData() ? $grid_left : $content;
        } else {
            return $this->isGetData() ? $grid_right : $content;
        }
    }

    private function column_grid($grid_type, $home_config_id)
    {
        $jump_id = request('jump_id', null);
        $fields = ['id', 'name'];
        if ($grid_type == 1) {
            $grid = new Grid(new Content('home_config_ids'));

            $grid->model()->where('config_id', $home_config_id);
//            if ($shelf_type == 1) {
//                $grid->model()->where('home_column_id', $home_column_id)->where('shelf_type', $shelf_type)
//                    ->join('home_configs', function (JoinClause $join) {
//                        $join->on('home_column_ids.column_id', '=', 'home_configs.id')->where('config_type', 1);
//                    })->select(['home_column_ids.id as id', 'home_column_ids.sort as sort', 'home_configs.title as name']);
//            } else if ($shelf_type == 2) {
//                $grid->model()->where('home_column_id', $home_column_id)->where('shelf_type', $shelf_type)
//                    ->join('home_configs', function (JoinClause $join) {
//                        $join->on('home_column_ids.column_id', '=', 'home_configs.id')->where('config_type', 2);
//                    })->select(['home_column_ids.id as id', 'home_column_ids.sort as sort', 'home_configs.title as name']);
//            } else if ($shelf_type == 3) {
//                $grid->model()->where('home_column_id', $home_column_id)->where('shelf_type', $shelf_type)
//                    ->join('goods', function (JoinClause $join) {
//                        $join->on('home_column_ids.column_id', '=', 'goods.id')->where('on_shelf', 1);
//                    })->select(['home_column_ids.id as id', 'home_column_ids.sort as sort', 'goods.name as name']);
//            }

            $grid->defaultSort('sort', 'asc');
            $grid->column('id', "ID")->width(60)->sortable();
        } else if ($grid_type == 2) {
            $home_jump = HomeJump::get()->toArray();
            $table_name = 'home_items';
            $quickOptions = null;
            $jump_info = null;
            if (count($home_jump)) {
                $jump_id = empty($jump_id) ? $home_jump[0]['id'] : $jump_id;
                foreach ($home_jump as $jump_item) {
                    $quickOptions[] = Radio::make($jump_item['id'], $jump_item['name']);
                    if ($jump_id == $jump_item['id']) {
                        $jump_info = $jump_item;
                    }
                }
                switch ($jump_info['form_type']) {
                    case 'input':
                    case 'textArea':
                    case 'wangEditor':
                        $table_name = 'home_items';
                        break;
                    case 'selectTable':
                        if (!empty($jump_info['table_info'])) {
                            $table_info = explode("\n", $jump_info['table_info']);
                            $fields = explode(",", $table_info[1]);
                            $table_name = $table_info[0];
                        }
                        break;
                }
            }


            request()->offsetSet('jump_id', null);

            $grid = new Grid(new Content($table_name));
            $grid->model()->select($fields);
//            $grid->model()->where('is_show', 1)->select(['id', 'name']);
            $grid->column('id', "ID")->width(60)->sortable();

            if ($quickOptions) {
                $grid->quickFilter()->filterKey('jump_id')
                    ->quickOptions($quickOptions, false)->defaultValue($home_jump[0]['id']);
            }
        }
        $grid->autoHeight();

        $grid->quickSearch(['name'])->quickSearchPlaceholder("名称");


        if ($grid_type == 1) {
            $grid->column('name', "名称");
            $grid->column('jump_id', "跳转类型")
                ->customValue(function ($row, $value) {
                    return GridCacheService::instance()->get_cache_value(HomeJump::class,
                        'home_jump_' . $value, $value, 'id', 'name');
                })->width(100);
            $grid->column('sort', '排序')->component(
                SortEdit::make()->action(config('admin.route.api_prefix') . '/entities/content/grid_sort_change?entity_id=9')
            )->width(100)->sortable();
            $grid->column('third_id', "关联ID")->width(90)->sortable();
        }else{
            $grid->column($fields[1], "名称");
        }

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->hideCreateButton();
        })->actions(function (Grid\Actions $actions) use ($grid_type, $home_config_id, $jump_id) {
            $actions->hideDeleteAction()->hideEditAction();

            $row = $actions->getRow();
            $isAction = false;

            if ($grid_type == 1) {
                $op_name = '取消关联';
            } else {
                $where = [
                    'config_id' => $home_config_id,
                    'jump_id' => $jump_id,
                    'third_id' => $row['id'],
                ];
                $homeConfigIdInfo = HomeConfigId::query()->where($where)->first();
                if (!empty($homeConfigIdInfo)) {
                    $isAction = true;
                    $op_name = '已关联';
                } else {
                    $op_name = '关联';
                }
            }

            $actions->add(Grid\Actions\ActionButton::make($op_name)->order(3)
                ->beforeEmit("tableSetLoading", true)
                ->successEmit("tableReload")
                ->afterEmit("tableSetLoading", false)
                ->handler(Grid\Actions\ActionButton::HANDLER_REQUEST)
                ->uri('/admin-api/home/config/relation?grid_type=' . $grid_type . '&jump_id='
                    . $jump_id . '&home_config_id=' . $home_config_id . '&third_id={id}')
                ->disabled($isAction)
                ->message('确认' . $op_name)
            );

        })->actionWidth(40);

        $grid->dataUrl("admin-api/home/config/relation_grid/{$home_config_id}?grid_type=" . $grid_type);

        return $grid;
    }

    public function relation()
    {
        $grid_type = request('grid_type');
        $jump_id = request('jump_id');
        $home_config_id = request('home_config_id');
        $third_id = request('third_id');

        if (empty($grid_type) || empty($home_config_id) || empty($third_id)) {
            return \Admin::responseError('关联失败，参数异常');
        }

        if ($grid_type == 1) {
            DB::table('home_config_ids')
                ->where('config_id', $home_config_id)
                //->where('jump_id', $jump_id)
                ->where('id', $third_id)
                ->delete();
            $data['action']['emit'] = 'tableReload';
            return \Admin::response($data, '取消关联成功');
        } else if ($grid_type == 2) {
            $count = DB::table('home_config_ids')
                ->where('config_id', $home_config_id)
                ->where('jump_id', $jump_id)
                ->where('third_id', $third_id)
                ->count('id');

            if ($count > 0) {
                return \Admin::responseError('已关联');
            } else {
                $table_name = 'home_items';
                $fields = ['id', 'name'];

                $jump_info = HomeJump::find($jump_id);
                if ($jump_info && $jump_info['table_info']) {
                    $table_info = explode("\n", $jump_info['table_info']);
                    $fields = explode(",", $table_info[1]);
                    $table_name = $table_info[0];
                }
                $model = new Content($table_name);
                $table_data = $model->select($fields)->find($third_id);

                $item['config_id'] = $home_config_id;
                $item['third_id'] = $third_id;
                $item['name'] = $table_data[$fields[1]];
                $item['jump_id'] = $jump_id;
                $item['data_type'] = $jump_info['data_type'];
                $item['updated_at'] = date('Y-m-d H:i:s', time());
                $item['created_at'] = date('Y-m-d H:i:s', time());
                $id = HomeConfigId::insertGetId($item);
                if ($id) {
                    $data['action']['emit'] = 'tableReload';
                    return \Admin::response($data, '关联成功');
                } else {
                    return \Admin::responseError('关联失败，操作异常');
                }
            }
        }
        return \Admin::responseError('操作异常');
    }
}

