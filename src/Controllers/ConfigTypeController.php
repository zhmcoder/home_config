<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Models\Entity;
use Andruby\DeepAdmin\Models\EntityField;
use Andruby\HomeConfig\Models\ConfigType;
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
        $grid = new Grid(new ConfigType());

        $grid->addDialogForm($this->form()->isDialog()->className('p-15'), '1000px');
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'), '1000px');

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
        $grid->column('form_type', '表单类型')->width(150);

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new ConfigType());
        $form->getActions()->buttonCenter();

        $form->item("name", "类型名称")->required()->inputWidth(8);
        $formType = config('deep_admin.form_type');
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
            )->required();
        $form->item("table_info", "配置项关联表")->component(
            Input::make()->textarea(5)->placeholder('对于表单类型为单选框、多选框、下拉选择的，需在此配置对应参数。参数格式为：key=value，多个以换行分隔。也可以填写自定义的函数名称，函数名称需以getFormItemsFrom开头，返回值需与前述数据格式一致。对于下拉选择远程搜索表单类型、短文本（input，自动完成）表单类型，需在此填写后端接口URL地址，接口返回数据格式可参考文档说明。')
        )->required(true, 'integer');

        return $form;
    }
}
