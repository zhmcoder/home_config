<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Models\Entity;
use Andruby\DeepAdmin\Models\EntityField;
use Andruby\HomeConfig\Models\HomeConfig;
use Andruby\HomeConfig\Models\HomeConfigType;
use Andruby\HomeConfig\Models\HomeItems;
use App\Models\AppInfo;
use App\Models\Goods;
use SmallRuralDog\Admin\Components\Attrs\SelectOption;
use SmallRuralDog\Admin\Components\Form\CSwitch;
use SmallRuralDog\Admin\Components\Form\Input;
use SmallRuralDog\Admin\Components\Form\RadioGroup;
use SmallRuralDog\Admin\Components\Form\Select;
use SmallRuralDog\Admin\Components\Form\Upload;
use SmallRuralDog\Admin\Components\Form\WangEditor;
use SmallRuralDog\Admin\Components\Grid\Boole;
use SmallRuralDog\Admin\Components\Grid\Image;
use SmallRuralDog\Admin\Controllers\AdminController;
use SmallRuralDog\Admin\Form;
use SmallRuralDog\Admin\Grid;

class ConfigTypeController extends ContentController
{
    public function grid()
    {
        $config_type = request('config_type', 1);
        $grid = new Grid(new HomeConfigType());
        $grid->model()->where('config_type', $config_type);
        $grid->addDialogForm($this->form()->isDialog()->className('p-15'), '800px');
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'), '800px');
        $grid->actions(function (Grid\Actions $actions) use ($config_type) {
            $row = $actions->getRow();

//            $actions->setDeleteAction(new Grid\Actions\DeleteDialogAction());

            if ($config_type == HomeConfigType::CONFIG_TYPE_HOME_STYLE) {
                $actions->add(Grid\Actions\ActionButton::make('数据管理')
                    ->handler(Grid\Actions\ActionButton::HANDLER_ROUTE)
                    ->uri('/home/item?config_id=' . $row['id'] . '&timestamp=' . time()));
            }


        })->actionWidth(100)->actionFixed('right');


        $grid->pageBackground()
            ->defaultSort('id', 'desc')
            ->quickSearch(['title'])
            ->stripe(true)
            ->fit(true)
            ->defaultSort('id', 'desc')
            ->perPage(env('PER_PAGE', 15))
            ->size(env('TABLE_SIZE', ''))
            ->border(env('TABLE_BORDER', false))
            ->emptyText("暂无数据");

        $grid->column("id", "序号")->width(80)->align('center')->sortable();
        $grid->column("name", "类型名称");
        if ($config_type == HomeConfigType::CONFIG_TYPE_ITEM_JUMP) {
            $grid->column('config_value', '类型值')->width(150);
        }


        return $grid;
    }

    public function form($isEdit = false)
    {
        $config_type = request('config_type', 1);
        $form = new Form(new HomeConfigType());
        $form->getActions()->buttonCenter();
        if ($config_type == HomeConfigType::CONFIG_TYPE_ITEM_JUMP) {
            $form->item("name", "跳转类型")->required()->inputWidth(8);
            $form->item("config_value", "类型值")->required()->inputWidth(8);
            $formType = config('home_config.form_type');
            $form->item('form_type', '表单类型')
                ->help('下拉选择（远程搜索）、下拉选择（多选，远程搜索）只支持行内展示')
                ->component(
                    Select::make()
                        ->filterable()
                        ->options(function () use ($formType) {
                            $return = [];
                            foreach ($formType as $key => $val) {
                                $return[] = SelectOption::make($key, $val);
                            }
                            return $return;
                        })
                );
            $form->item("table_info", "配置项关联表")->component(
                Input::make()->textarea(5)->placeholder('对于表单类型为单选框、多选框、下拉选择的，需在此配置对应参数。参数格式为：key=value，多个以换行分隔。也可以填写自定义的函数名称，函数名称需以getFormItemsFrom开头，返回值需与前述数据格式一致。对于下拉选择远程搜索表单类型、短文本（input，自动完成）表单类型，需在此填写后端接口URL地址，接口返回数据格式可参考文档说明。')
            );
        } else {
            $form->item("name", "样式名称")->required()->inputWidth(8);

        }

        $form->item('config_type', '')->component(
            Input::make($config_type)->type('hidden')
        )->hideLabel();

        return $form;
    }
}
