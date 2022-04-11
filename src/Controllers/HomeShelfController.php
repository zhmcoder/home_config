<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Attrs\SelectOption;
use Andruby\DeepAdmin\Components\Form\Select;
use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\HomeConfig\Models\HomeShelf;
use Andruby\DeepAdmin\Components\Form\Upload;
use Andruby\DeepAdmin\Components\Grid\Image;
use Andruby\DeepAdmin\Form;
use Andruby\DeepAdmin\Grid;

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
        $grid->column('type', '类型')->customValue(function ($row) {
            if (key_exists($row['type'], HomeShelf::TYPE)) {
                return HomeShelf::TYPE[$row['type']];
            } else {
                return $row['type'];
            }
        });
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

        $options = [];
        foreach (HomeShelf::TYPE as $key => $value) {
            $options[] = SelectOption::make($key, $value);
        }
        $form->item("type", "类型")->component(
            Select::make()->options($options)->clearable()->filterable()
        );

        $form->item("image", '货架样式示例')->required()->component(
            Upload::make()->width(80)->height(80)
        )->help('建议上传货架效果图，并标题内容。')
            ->inputWidth(24);

        return $form;
    }
}
