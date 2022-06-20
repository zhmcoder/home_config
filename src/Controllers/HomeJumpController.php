<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\HomeConfig\Models\HomeJump;
use Andruby\HomeConfig\Services\AppInfoService;
use App\Admin\Services\GridCacheService;
use App\Models\AdminRoleUser;
use Illuminate\Http\Request;
use Andruby\DeepAdmin\Components\Attrs\SelectOption;
use Andruby\DeepAdmin\Components\Form\Input;
use Andruby\DeepAdmin\Components\Form\Select;
use Andruby\DeepAdmin\Components\Grid\Tag;
use Andruby\DeepAdmin\Form;
use Andruby\DeepAdmin\Grid;

class HomeJumpController extends ContentController
{
    public function grid()
    {
        $grid = new Grid(new HomeJump());

        $grid->model()->where(function ($query) {
            $user = \Admin::user();
            if ($user['role_id'] > AdminRoleUser::ROLE_ADMINISTRATOR) {
                $showApp = json_decode($user['show_app'], true);
                foreach ($showApp as $appId) {
                    $query->orWhere('show_app', 'like', '%"' . $appId . '"%');
                }
            }
        });

        $grid->addDialogForm($this->form()->isDialog()->className('p-15'), '800px');
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'), '800px');

        $grid->actions(function (Grid\Actions $actions) {
            $row = $actions->getRow();
            if ($row['data_type'] == 1) {
                $actions->add(Grid\Actions\ActionButton::make('数据管理')
                    ->handler(Grid\Actions\ActionButton::HANDLER_ROUTE)
                    ->uri('/home/item?config_id=' . $row['id'] . '&timestamp=' . time()));
            }

        })->actionWidth(100)->actionFixed('right');

        $grid->quickSearch(['name']);
        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('show_app', '展示app')->component(
                Select::make()->options(function () {
                    return AppInfoService::instance()->app_info();
                })->clearable()->filterable()
            );
        });

        $grid->column("id", "序号")->width(80)->align('center')->sortable();
        $grid->column("name", "类型名称");

        $formType = config('home_config.form_type');
        $grid->column("form_type", "关联数据类型")
            ->customValue(function ($row, $value) use ($formType) {
                return $formType[$row['data_type']][$value];
            })->component(Tag::make()->type($formType));

        $dataType = config('home_config.data_type');
        $grid->column("data_type", "表单类型")
            ->customValue(function ($row, $value) use ($dataType) {
                return $dataType[$value];
            })->component(Tag::make()->type($dataType));

        $grid->column('show_app', '展示app')->customValue(function ($row, $value) {
            $appInfo = [];
            foreach ($value as $appId) {
                $appInfo[] = GridCacheService::instance()->app_name($appId);
            }
            return $appInfo;
        })->component(Tag::make())->width(200);

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new HomeJump());
        $form->getActions()->buttonCenter();
        $form->labelWidth('200px');

        $form->item("name", "跳转类型")->required()->inputWidth(8);

        $dataType = config('home_config.data_type');
        $form->item('data_type', '关联数据类型')
            ->component(
                Select::make()->filterable()
                    ->isRelatedSelect(true)
                    ->relatedSelectRef('form_type')
                    ->ref('data_type')
                    ->options(function () use ($dataType) {
                        $return = [];
                        foreach ($dataType as $key => $val) {
                            $return[] = SelectOption::make($key, $val);
                        }
                        return $return;
                    })
            );

        $remoteUrl = config('deep_admin.route.api_prefix') . '/home/jump/form_type';
        $form->item('form_type', '表单类型')
            ->help('下拉单择（连表查询）必须输入表名，字段(id，名称、图片)、查询条件(key,op,value)')
            ->component(Select::make()->filterable()->remote($remoteUrl)->ref('form_type'))
            ->inputWidth(24);

        $form->item("table_info", "配置项关联表")->component(
            Input::make()->textarea(5)
                ->placeholder('下拉单择（连表查询）必须输入表名。格式如下：第一行表名,如shops;第二行字段包括唯一标识、名称、图片，如id、name、image;')
        )->vif('form_type', 'selectTable');

        $form->item('show_app', '展示app')->component(
            Select::make()->options(function () {
                return AppInfoService::instance()->app_info();
            })->clearable()->filterable()->multiple()
        )->inputWidth(24);

        return $form;
    }

    public function form_type(Request $request)
    {
        $data_type = $request->input('data_type');

        $formType = config('home_config.form_type');

        $list = null;

        foreach ($formType[$data_type] as $key => $value) {
            $list[] = ['value' => $key, 'label' => $value];
        }

        return [
            'data' => [
                'total' => $list ? count($list) : 0,
                'data' => $list
            ],
        ];
    }
}
