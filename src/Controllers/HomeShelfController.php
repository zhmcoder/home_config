<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\HomeConfig\Models\HomeShelf;
use SmallRuralDog\Admin\Components\Form\Upload;
use SmallRuralDog\Admin\Components\Grid\Image;
use SmallRuralDog\Admin\Form;
use SmallRuralDog\Admin\Grid;

class HomeShelfController extends ContentController
{
    public function grid()
    {
        $grid = new Grid(new HomeShelf());
        $grid->addDialogForm($this->form()->isDialog()->className('p-15'), '600px');
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'), '600px');

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
        $grid->column("name", "货架名称");
        $grid->column("image", "货架样式示例")->component(
            Image::make()->size(50, 50)->preview()
        )->align("center");

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new HomeShelf());
        $form->labelWidth('200px');
        $form->getActions()->buttonCenter();

        $form->item("name", "货架名称")->required()->inputWidth(8);
        $form->item("image", '货架样式示例')->required()->component(
            Upload::make()->width(80)->height(80)
        )->help('建议上传货架效果图，并标题内容。')
            ->inputWidth(24);

        return $form;
    }
}
