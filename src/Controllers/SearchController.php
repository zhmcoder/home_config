<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Form\Select;
use Andruby\DeepAdmin\Components\Grid\SortEdit;
use Andruby\DeepAdmin\Components\Grid\Tag;
use Andruby\HomeConfig\Models\Search;
use Andruby\DeepAdmin\Controllers\AdminController;
use Andruby\DeepAdmin\Form;
use Andruby\DeepAdmin\Grid;
use Andruby\HomeConfig\Services\AppInfoService;
use App\Admin\Services\GridCacheService;
use App\Models\AdminRoleUser;

class SearchController extends AdminController
{
    public function grid()
    {
        $grid = new Grid(new Search());

        $grid->model()->where(function ($query) {
            $user = \Admin::user();
            if ($user['role_id'] > AdminRoleUser::ROLE_ADMINISTRATOR) {
                $showApp = json_decode($user['show_app'], true);
                foreach ($showApp as $appId) {
                    $query->orWhere('show_app', 'like', '%"' . $appId . '"%');
                }
            }
        });

        $grid->addDialogForm($this->form()->isDialog()->className('p-15'));
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'));

        $grid->quickSearch(['name']);
        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('show_app', '展示app')->component(
                Select::make()->options(function () {
                    return AppInfoService::instance()->app_info();
                })->clearable()->filterable()
            );
        });

        $grid->column("id", "序号")->width(80)->align('center')->sortable();
        $grid->column("name", "名称");
        $grid->column('sort', '排序')->component(
        //SortUpDown::make(100)->setSortAction(config('deep_admin.route.api_prefix') . '/entities/content/sort_up_down?entity_id=7')
            SortEdit::make()->action(config('deep_admin.route.api_prefix') . '/entities/content/grid_sort_change?entity_id=7')
        )->sortable();

        $grid->column('show_app', '展示app')->customValue(function ($row, $value) {
            $appInfo = [];
            foreach ($value as $appId) {
                $appInfo[] = GridCacheService::instance()->app_name($appId);
            }
            return $appInfo;
        })->component(Tag::make())->width(200);

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
        $form->labelWidth('150px');

        $form->item("name", "名称")->required()->inputWidth(15);

        $form->item('show_app', '展示app')->component(
            Select::make()->options(function () {
                return AppInfoService::instance()->app_info();
            })->clearable()->filterable()->multiple()
        )->inputWidth(24)->required(true, 'array');

        return $form;
    }
}
