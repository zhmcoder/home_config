<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\HomeConfig\Models\HomeJump;
use SmallRuralDog\Admin\Components\Attrs\SelectOption;
use SmallRuralDog\Admin\Components\Form\Input;
use SmallRuralDog\Admin\Components\Form\Select;
use SmallRuralDog\Admin\Components\Grid\Tag;
use SmallRuralDog\Admin\Form;
use SmallRuralDog\Admin\Grid;

class HomeJumpController extends ContentController
{
    public function grid()
    {
        $grid = new Grid(new HomeJump());
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

        $grid->pageBackground()
            ->defaultSort('id', 'desc')
            ->quickSearch(['name'])
            ->stripe(true)
            ->fit(true)
            ->perPage(env('PER_PAGE', 15))
            ->size(env('TABLE_SIZE', ''))
            ->border(env('TABLE_BORDER', false))
            ->emptyText("暂无数据");

        $grid->column("id", "序号")->width(80)->align('center')->sortable();
        $grid->column("name", "类型名称");

        $formType = config('home_config.form_type');
        $grid->column("form_type", "关联数据类型")
            ->customValue(function ($row, $value) use ($formType) {
                return $formType[$value];
            })->component(Tag::make()->type($formType));

        $dataType = config('home_config.data_type');
        $grid->column("data_type", "表单类型")
            ->customValue(function ($row, $value) use ($dataType) {
                return $dataType[$value];
            })->component(Tag::make()->type($dataType));

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new HomeJump());
        $form->getActions()->buttonCenter();
        $form->item("name", "跳转类型")->required()->inputWidth(8);

        $dataType = config('home_config.data_type');
        $form->item('data_type', '关联数据类型')
            ->component(
                Select::make()
                    ->filterable()
                    ->options(function () use ($dataType) {
                        $return = [];
                        foreach ($dataType as $key => $val) {
                            $return[] = SelectOption::make($key, $val);
                        }
                        return $return;
                    })
            );

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
        )->vif('form_type', 'selectTable');

        return $form;
    }
}
