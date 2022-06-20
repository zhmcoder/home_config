<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Components\Attrs\SelectOption;
use Andruby\DeepAdmin\Components\Form\Select;
use Andruby\DeepAdmin\Components\Grid\Tag;
use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\HomeConfig\Models\HomeShelf;
use Andruby\DeepAdmin\Components\Form\Upload;
use Andruby\DeepAdmin\Components\Grid\Image;
use Andruby\DeepAdmin\Form;
use Andruby\DeepAdmin\Grid;
use Andruby\HomeConfig\Services\AppInfoService;
use App\Admin\Services\GridCacheService;
use App\Models\AdminRoleUser;

class HomeShelfController extends ContentController
{
    public function grid()
    {
        $grid = new Grid(new HomeShelf());

        $grid->model()->where(function ($query) {
            $user = \Admin::user();
            if ($user['role_id'] > AdminRoleUser::ROLE_ADMINISTRATOR) {
                $showApp = json_decode($user['show_app'], true);
                foreach ($showApp as $appId) {
                    $query->orWhere('show_app', 'like', '%"' . $appId . '"%');
                }
            }
        });

        $grid->addDialogForm($this->form()->isDialog()->className('p-15'), '600px');
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'), '600px');

        $grid->quickSearch(['name']);
        $grid->filter(function (Grid\Filter $filter) {
            $filter->like('show_app', '展示app')->component(
                Select::make()->options(function () {
                    return AppInfoService::instance()->app_info();
                })->clearable()->filterable()
            );
        });

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

        $grid->column('show_app', '展示app')->customValue(function ($row, $value) {
            $appInfo = [];
            foreach ($value as $appId) {
                $appInfo[] = GridCacheService::instance()->app_name($appId);
            }
            return $appInfo;
        })->component(Tag::make())->width(200);

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

        $form->item('show_app', '展示app')->component(
            Select::make()->options(function () {
                return AppInfoService::instance()->app_info();
            })->clearable()->filterable()->multiple()
        )->inputWidth(24);

        return $form;
    }
}
