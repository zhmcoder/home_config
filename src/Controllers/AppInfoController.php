<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Grid;
use App\Models\AdminRoleUser;

class AppInfoController extends ContentController
{
    protected function getTableName()
    {
        return 'app_infos';
    }

    protected function grid_list(Grid $grid)
    {
        $grid->model()->where(function ($query) {
            $user = \Admin::user();
            if ($user['role_id'] > AdminRoleUser::ROLE_ADMINISTRATOR) {
                $showApp = json_decode($user['show_app'], true);
                foreach ($showApp as $appId) {
                    $query->orWhere('app_id', $appId);
                }
            }
        });

        $grid->addDialogForm($this->form()->isDialog()->className('p-15'));
        $grid->editDialogForm($this->form(true)->isDialog()->className('p-15'));

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->createButton()->content("添加应用");
        })->actions(function (Grid\Actions $actions) {
            $actions->setDeleteAction(new Grid\Actions\DeleteDialogAction());
        });

        return $grid;
    }
}
