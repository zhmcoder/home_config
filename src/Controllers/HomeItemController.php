<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Grid\Tag;
use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Models\Content;
use Andruby\DeepAdmin\Models\EntityField;
use Andruby\HomeConfig\Models\HomeJump;
use Andruby\HomeConfig\Models\HomeItem;
use Andruby\DeepAdmin\Components\Attrs\SelectOption;
use Andruby\DeepAdmin\Components\Form\CSwitch;
use Andruby\DeepAdmin\Components\Form\Input;
use Andruby\DeepAdmin\Components\Form\Select;
use Andruby\DeepAdmin\Components\Form\Upload;
use Andruby\DeepAdmin\Components\Form\WangEditor;
use Andruby\DeepAdmin\Components\Grid\Boole;
use Andruby\DeepAdmin\Components\Grid\Image;
use Andruby\DeepAdmin\Form;
use Andruby\DeepAdmin\Grid;
use Andruby\HomeConfig\Services\AppInfoService;
use App\Admin\Services\GridCacheService;
use App\Models\AdminRoleUser;

class HomeItemController extends ContentController
{
    public function grid()
    {
        $config_id = request('config_id', 1);
        $grid = new Grid(new HomeItem());

        $grid->model()->where('config_id', $config_id);
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

        $grid->quickSearch(['name']);
        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('show_app', '展示app')->component(
                Select::make()->options(function () {
                    return AppInfoService::instance()->app_info();
                })->clearable()->filterable()
            );
        });

        $grid->column("id", "序号")->width(80)->align('center')->sortable();
        $grid->column("name", "标题")->sortable();
//        $grid->column("config_id", "货架名称")->customValue(function ($row, $value) {
//            return GridCacheService::instance()->get_cache_value(HomeJump::class,
//                'home_config_type_' . $value, $value, 'id', 'name');
//        });

        $grid->column("image", '图片')->component(
            Image::make()->size(50, 50)->preview()
        )->width(150)->align("center");

        $grid->column("is_show", "是否显示")->component(Boole::make())->width(150);


//        $grid->column('jump_type', '跳转类型')->customValue(function ($row, $value) {
//            return GridCacheService::instance()->get_cache_value(HomeJump::class,
//                'home_config_type_' . $value, $value, 'config_value', 'name');;
//        });

        $grid->column('show_app', '展示app')->customValue(function ($row, $value) {
            $appInfo = [];
            foreach ($value as $appId) {
                $appInfo[] = GridCacheService::instance()->app_name($appId);
            }
            return $appInfo;
        })->component(Tag::make())->width(200);

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->createButton()->content("添加");
        });

        return $grid;
    }

    public function form($isEdit = false)
    {
        $config_id = request('config_id', 2);
//        $name = HomeItems::CONFIG_TYPE[$config_type];

        $form = new Form(new HomeItem());
        $form->getActions()->buttonCenter();

        $form->item("name", "标题")->required()->inputWidth(8);

        $form->item("image", '图片')->required()->component(
            Upload::make()->width(80)->height(80)
        )->help('建议图片大小: 50*50, 圆形');

        $form->item("is_show", "是否显示")->inputWidth(10)->component(CSwitch::make());

        $fields = EntityField::query()->where('name', 'jump_id')->first();
        $option = $this->_selectOptionTable($fields);
        $form->item("jump_id", "跳转类型")->component(
            Select::make()->options($option)
        )->required(true, 'integer');

        $jump_list = HomeJump::where('data_type', 1)->get()->toArray();
        foreach ($jump_list as $item) {
            switch ($item['form_type']) {
                case 'input':
                    $form->item('content', $item['name'])->component(
                        Input::make()->showWordLimit()
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
                case 'textArea':
                    $form->item('content', $item['name'])->component(
                        Input::make()->textarea(4)->showWordLimit()
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
                case 'wangEditor':
                    $form->item('content', $item['name'])->component(
                        WangEditor::make()->uploadImgServer($this->uploadImages)->uploadFileName('file')->style('min-height:300px;')
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
                case 'selectTable':
                    $options = [];
                    if (!empty($item['table_info'])) {
                        $table_info = explode("\n", $item['table_info']);
                        $fields = explode(",", $table_info[1]);
                        $table = new Content($table_info[0]);
                        $option_data = $table->get($fields)->toArray();
                        foreach ($option_data as $option) {
                            $options[] = SelectOption::make($option[$fields[0]], $option[$fields[1]]);
                        }
                    }

                    $form->item('content', $item['name'])->component(
                        Select::make()->options($options)
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
            }
        }

        $form->item('config_id', '')->component(
            Input::make($config_id)->type('hidden')
        )->hideLabel();

        $form->item('show_app', '展示app')->component(
            Select::make()->options(function () {
                return AppInfoService::instance()->app_info();
            })->clearable()->filterable()->multiple()
        )->inputWidth(24)->required(true, 'array');

        return $form;
    }
}
