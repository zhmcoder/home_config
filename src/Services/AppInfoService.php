<?php

namespace Andruby\HomeConfig\Services;

use Andruby\DeepAdmin\Components\Attrs\SelectOption;
use Andruby\HomeConfig\Models\AppInfo;
use App\Models\AdminRoleUser;

/**
 * @method static AppInfoService instance()
 *
 * Class AppInfoService
 * @package Andruby\HomeConfig\Services
 */
class AppInfoService
{
    public static function __callStatic($method, $params): AppInfoService
    {
        return new self();
    }

    public function app_info()
    {
        return AppInfo::query()->where(function ($query) {
            $user = \Admin::user();
            if ($user['role_id'] > AdminRoleUser::ROLE_ADMINISTRATOR) {
                $showApp = json_decode($user['show_app'], true);
                foreach ($showApp as $appId) {
                    $query->orWhere('app_id', $appId);
                }
            }
        })->get()->map(function ($item) {
            return SelectOption::make($item->app_id, $item->name);
        })->all();
    }

}
