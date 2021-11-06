<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Grid\SortEdit;
use Andruby\DeepAdmin\Services\GridCacheService;
use Andruby\HomeConfig\Models\HomeConfig;
use Andruby\HomeConfig\Models\HomeJump;
use Andruby\HomeConfig\Models\HomeItems;
use Andruby\HomeConfig\Models\HomeConfigIds;
use Andruby\HomeConfig\Models\HomeShelf;
use Illuminate\Support\Facades\DB;
use SmallRuralDog\Admin\Components\Widgets\Card;
use Andruby\DeepAdmin\Controllers\ContentController;
use SmallRuralDog\Admin\Grid;
use Andruby\DeepAdmin\Models\Content;
use SmallRuralDog\Admin\Layout\Row;
use Illuminate\Database\Query\JoinClause;

// 货架关联
class HomeConfigController extends ContentController
{
    protected function grid_action(Grid\Actions $actions)
    {
        $row = $actions->getRow();

//        $title = HomeConfig::SHELF_TYPE[$row['shelf_type']];
        $title = GridCacheService::instance()->get_cache_value(HomeShelf::class,
            'home_shelf_' . $row['shelf_type'], $row['shelf_type'], 'id', 'name');
        $actions->add(Grid\Actions\ActionButton::make('关联数据')
            ->handler(Grid\Actions\ActionButton::HANDLER_ROUTE)
            ->uri('/home/config/relation_grid/{id}?shelf_type=' . $row['shelf_type'].'&entity_id=9' . '&timestamp=' . time()));

        $actions->setDeleteAction(new Grid\Actions\DeleteDialogAction())->params('entity_id=9');
    }

    public function relation_grid(\SmallRuralDog\Admin\Layout\Content $content, $home_column_id = null)
    {
        $grid_type = request('grid_type');
        $shelf_type = request('shelf_type');

        $grid_left = $this->column_grid(1, $home_column_id, $shelf_type);
        $grid_right = $this->column_grid(2, $home_column_id, $shelf_type);

        $homeColumnInfo = HomeItems::query()->find($home_column_id);

        $content->row(function (Row $row) use ($grid_left, $grid_right, $homeColumnInfo) {
            $row->gutter(0)->className('mt-10');
            $row->column(12, Card::make()->header('货架：' . $homeColumnInfo['name'] . '，已关联内容')->bodyStyle('padding:0px;margin_left:100px;')->content(
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

    private function column_grid($grid_type, $home_column_id, $shelf_type)
    {
        if ($grid_type == 1) {
            $grid = new Grid(new Content('home_column_ids'));

            if ($shelf_type == 1) {
                $grid->model()->where('home_column_id', $home_column_id)->where('shelf_type', $shelf_type)
                    ->join('home_configs', function (JoinClause $join) {
                        $join->on('home_column_ids.column_id', '=', 'home_configs.id')->where('config_type', 1);
                    })->select(['home_column_ids.id as id', 'home_column_ids.sort as sort', 'home_configs.title as name']);
            } else if ($shelf_type == 2) {
                $grid->model()->where('home_column_id', $home_column_id)->where('shelf_type', $shelf_type)
                    ->join('home_configs', function (JoinClause $join) {
                        $join->on('home_column_ids.column_id', '=', 'home_configs.id')->where('config_type', 2);
                    })->select(['home_column_ids.id as id', 'home_column_ids.sort as sort', 'home_configs.title as name']);
            } else if ($shelf_type == 3) {
                $grid->model()->where('home_column_id', $home_column_id)->where('shelf_type', $shelf_type)
                    ->join('goods', function (JoinClause $join) {
                        $join->on('home_column_ids.column_id', '=', 'goods.id')->where('on_shelf', 1);
                    })->select(['home_column_ids.id as id', 'home_column_ids.sort as sort', 'goods.name as name']);
            }

            $grid->defaultSort('sort', 'asc');
            $grid->column('id', "ID")->width(80)->sortable();

        } else if ($grid_type == 2) {
            if ($shelf_type == 1) {
                $grid = new Grid(new Content('home_configs'));
                $grid->model()->where('config_type', 1)->select(['id', 'title as name']);
            } else if ($shelf_type == 2) {
                $grid = new Grid(new Content('home_configs'));
                $grid->model()->where('config_type', 2)->select(['id', 'title as name']);
            } else if ($shelf_type == 3) {
                $grid = new Grid(new Content('goods'));
                $grid->model()->where('on_shelf', 1)->select(['id', 'name']);
            }
        }
        $grid->autoHeight();
        $grid->quickSearch(['name'])->quickSearchPlaceholder("名称");

        $grid->column('name', "名称")->sortable();
        if ($grid_type == 1) {
            $grid->column('sort', '排序')->component(
                SortEdit::make()->action(config('admin.route.api_prefix') . '/entities/content/grid_sort_change?entity_id=55')
            )->width(100)->sortable();
        }

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->hideCreateButton();
        })->actions(function (Grid\Actions $actions) use ($grid_type, $home_column_id, $shelf_type) {
            $actions->hideDeleteAction()->hideEditAction();

            $row = $actions->getRow();
            $isAction = false;

            if ($grid_type == 1) {
                $op_name = '取消关联';
            } else {
                if (isset($row['home_column_id'])) {
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
                ->uri('/admin-api/home/config/relation?grid_type=' . $grid_type . '&shelf_type=' . $shelf_type . '&home_column_id=' . $home_column_id . '&column_id={id}')
                ->disabled($isAction)
                ->message('确认' . $op_name)
            );

        })->actionWidth(30);

        $grid->dataUrl("admin-api/home/config/relation_grid/{$home_column_id}?grid_type=" . $grid_type);

        return $grid;
    }

    public function relation()
    {
        $grid_type = request('grid_type');
        $shelf_type = request('shelf_type');
        $home_column_id = request('home_column_id');
        $column_id = request('column_id');

        if (empty($grid_type) || empty($home_column_id) || empty($column_id)) {
            return \Admin::responseError('关联失败，参数异常');
        }

        if ($grid_type == 1) {
            DB::table('home_column_ids')
                ->where('home_column_id', $home_column_id)
                ->where('shelf_type', $shelf_type)
                ->where('id', $column_id)
                ->delete();
            $data['action']['emit'] = 'tableReload';
            return \Admin::response($data, '取消关联成功');
        } else if ($grid_type == 2) {
            $count = DB::table('home_column_ids')
                ->where('home_column_id', $home_column_id)
                ->where('shelf_type', $shelf_type)
                ->where('column_id', $column_id)
                ->count('id');

            if ($count > 0) {
                return \Admin::responseError('已关联');
            } else {
                $item['home_column_id'] = $home_column_id;
                $item['column_id'] = $column_id;
                $item['shelf_type'] = $shelf_type;
                $item['updated_at'] = date('Y-m-d H:i:s', time());
                $item['created_at'] = date('Y-m-d H:i:s', time());
                $id = HomeConfigIds::insertGetId($item);
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

