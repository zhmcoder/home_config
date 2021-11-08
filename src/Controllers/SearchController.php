<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Grid\SortEdit;
use Andruby\DeepAdmin\Components\Grid\SortUpDown;
use Andruby\HomeConfig\Models\Search;
use SmallRuralDog\Admin\Controllers\AdminController;
use SmallRuralDog\Admin\Form;
use SmallRuralDog\Admin\Grid;

class SearchController extends AdminController
{
    public function grid()
    {
        $grid = new Grid(new Search());

        $grid->addDialogForm($this->form()->isDialog()->className('p-15'));
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'));

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
        $grid->column("name", "名称");
        $grid->column('sort', '排序')->component(
        //SortUpDown::make(100)->setSortAction(config('admin.route.api_prefix') . '/entities/content/sort_up_down?entity_id=7')
            SortEdit::make()->action(config('admin.route.api_prefix') . '/entities/content/grid_sort_change?entity_id=7')
        );

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->createButton()->content("添加搜索");
        })->actions(function (Grid\Actions $actions) {
            $actions->setDeleteAction(new Grid\Actions\DeleteDialogAction());
        });

        return $grid;
    }

    public function form($isEdit = false)
    {
        $form = new Form(new Search());
        $form->getActions()->buttonCenter();

        $form->item("name", "名称")->required()->inputWidth(15);

        return $form;
    }
}
