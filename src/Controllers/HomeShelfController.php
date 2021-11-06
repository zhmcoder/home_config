<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Models\Entity;
use Andruby\DeepAdmin\Models\EntityField;
use Andruby\HomeConfig\Models\HomeConfig;
use Andruby\HomeConfig\Models\HomeJump;
use Andruby\HomeConfig\Models\HomeItem;
use Andruby\HomeConfig\Models\HomeShelf;
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

class HomeShelfController extends ContentController
{
    public function grid()
    {
        $grid = new Grid(new HomeShelf());
        $grid->addDialogForm($this->form()->isDialog()->className('p-15'), '800px');
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'), '800px');

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
        $grid->column("name", "货架名称");

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new HomeShelf());
        $form->getActions()->buttonCenter();
        $form->item("name", "货架名称")->required()->inputWidth(8);
        $form->item("thumb", '货架样式示例')->required()->component(
            Upload::make()->width(80)->height(80)
        )->help('建议上传货架效果图，并标题内容。');

        return $form;
    }
}
