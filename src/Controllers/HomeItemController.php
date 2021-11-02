<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Models\EntityField;
use App\Models\Goods;
use Andruby\HomeConfig\Models\HomeItems;
use SmallRuralDog\Admin\Components\Attrs\SelectOption;
use SmallRuralDog\Admin\Components\Form\CSwitch;
use SmallRuralDog\Admin\Components\Form\Input;
use SmallRuralDog\Admin\Components\Form\RadioGroup;
use SmallRuralDog\Admin\Components\Form\Select;
use SmallRuralDog\Admin\Components\Form\Upload;
use SmallRuralDog\Admin\Components\Form\WangEditor;
use SmallRuralDog\Admin\Components\Grid\Boole;
use SmallRuralDog\Admin\Components\Grid\Image;
use SmallRuralDog\Admin\Form;
use SmallRuralDog\Admin\Grid;

class HomeItemController extends ContentController
{
    public function grid()
    {
        $config_type = request('config_type', 2);
        $name = HomeItems::CONFIG_TYPE[$config_type];

        $grid = new Grid(new HomeItems());
        $grid->model()->where('config_type', $config_type);

        $grid->addDialogForm($this->form()->actionParams(['config_type' => $config_type])->isDialog()->className('p-15'), '1000px');
        $grid->editDialogForm($this->form(true)->actionParams(['config_type' => $config_type])->isDialog()->className('p-15'), '1000px');

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

        $grid->column("title", "标题")->sortable();

        $grid->column("thumb", $name)->component(
            Image::make()->size(50, 50)->preview()
        )->width(150)->align("center");

        $grid->column("is_show", "是否显示")->component(Boole::make())->width(150);

        $fields = EntityField::query()->find(291);
        $option = $this->_selectOptionList($fields);
        $grid->column('jump_type', '跳转类型')->customValue(function ($row, $value) use ($option) {
            return $option['option'][$value] ?? '';
        });

        $grid->toolbars(function (Grid\Toolbars $toolbars) use ($name) {
            $toolbars->createButton()->content("添加" . $name);
        });

        return $grid;
    }

    public function form($isEdit = false)
    {
        $config_type = request('config_type', 2);
        $name = HomeItems::CONFIG_TYPE[$config_type];

        $form = new Form(new HomeItems());
        $form->getActions()->buttonCenter();

        $form->item("title", "标题")->required()->inputWidth(8);

        $form->item("thumb", $name)->required()->component(
            Upload::make()->width(80)->height(80)
        )->help('建议图片大小: 50*50, 圆形');

        $form->item("is_show", "是否显示")->inputWidth(10)->component(CSwitch::make());

        $fields = EntityField::query()->find(291);
        $option = $this->_radio($fields);
        $form->item("jump_type", "跳转类型")->component(
            RadioGroup::make(0, $option)
        )->required(true, 'integer');

        $form->item('content', 'H5详情')->component(
            WangEditor::make()->uploadImgServer($this->uploadImages)->uploadFileName('file')->style('min-height:300px;')
        )->inputWidth(15)->vif('jump_type', 0);

        $form->item('h5_url', 'H5链接')->component(
            Input::make()->textarea(4)->showWordLimit()
        )->inputWidth(15)->vif('jump_type', 3);

        $form->item('relation_id', "商品")->required(true, 'integer')->serveRules("min:1")->component(Select::make(null)->filterable()->options(function () {
            return Goods::query()->orderByDesc('id')->where('on_shelf', 1)->get()->map(function ($item) {
                return SelectOption::make($item->id, $item->name);
            })->all();
        }))->vif('jump_type', 1);

//        $form->item('relation_id', "店铺")->required(true, 'integer')->serveRules("min:1")->component(Select::make(null)->filterable()->options(function () {
//            return Shop::query()->orderByDesc('id')->get()->map(function ($item) {
//                return SelectOption::make($item->id, $item->name);
//            })->all();
//        }))->vif('jump_type', 2);

        $form->item('config_type', 'config_type')->hideLabel()->component(Input::make($config_type)->type('hidden'));

        return $form;
    }
}
