<?php

namespace Andruby\HomeConfig\Controllers;

use Andruby\DeepAdmin\Controllers\ContentController;
use Andruby\DeepAdmin\Models\Content;
use Andruby\DeepAdmin\Models\EntityField;
use Andruby\DeepAdmin\Services\GridCacheService;
use Andruby\HomeConfig\Models\AppInfo;
use Andruby\HomeConfig\Models\HomeJump;
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
        $config_id = request('config_id', 1);
        $grid = new Grid(new HomeItems());
        $grid->model()->where('config_id', $config_id);

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
        $grid->column("title", "标题")->sortable();
//        $grid->column("config_id", "货架名称")->customValue(function ($row, $value) {
//            return GridCacheService::instance()->get_cache_value(HomeJump::class,
//                'home_config_type_' . $value, $value, 'id', 'name');
//        });

        $grid->column("thumb", '图片')->component(
            Image::make()->size(50, 50)->preview()
        )->width(150)->align("center");

        $grid->column("is_show", "是否显示")->component(Boole::make())->width(150);


//        $grid->column('jump_type', '跳转类型')->customValue(function ($row, $value) {
//            return GridCacheService::instance()->get_cache_value(HomeJump::class,
//                'home_config_type_' . $value, $value, 'config_value', 'name');;
//        });

        $grid->toolbars(function (Grid\Toolbars $toolbars) {
            $toolbars->createButton()->content("添加");
        });

        return $grid;
    }

    public function form($isEdit = false)
    {
        $config_id = request('config_id', 2);
//        $name = HomeItems::CONFIG_TYPE[$config_type];

        $form = new Form(new HomeItems());
        $form->getActions()->buttonCenter();

        $form->item("title", "标题")->required()->inputWidth(8);

        $form->item("thumb", '图片')->required()->component(
            Upload::make()->width(80)->height(80)
        )->help('建议图片大小: 50*50, 圆形');

        $form->item("is_show", "是否显示")->inputWidth(10)->component(CSwitch::make());

        $fields = EntityField::query()->where('name', 'jump_id')->first();
        $option = $this->_selectOptionTable($fields);
        $form->item("jump_id", "跳转类型")->component(
            Select::make()->options($option)
        )->required(true, 'integer');

        $jump_list = HomeJump::where('data_type', 1)->get()->toArray();
        foreach ($jump_list as $item) {
            switch ($item['form_type']) {
                case 'input':
                    $form->item('content', $item['name'])->component(
                        Input::make()->showWordLimit()
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
                case 'textArea':
                    $form->item('content', $item['name'])->component(
                        Input::make()->textarea(4)->showWordLimit()
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
                case 'wangEditor':
                    $form->item('content', $item['name'])->component(
                        WangEditor::make()->uploadImgServer($this->uploadImages)->uploadFileName('file')->style('min-height:300px;')
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
                case 'selectTable':
                    $options = [];
                    if (!empty($item['table_info'])) {
                        $table_info = explode("\n", $item['table_info']);
                        $fields = explode(",", $table_info[1]);
                        $table = new Content($table_info[0]);
                        $option_data = $table->get($fields)->toArray();
                        foreach ($option_data as $option) {
                            $options[] = SelectOption::make($option[$fields[0]], $option[$fields[1]]);
                        }
                    }

                    $form->item('content', $item['name'])->component(
                        Select::make()->options($options)
                    )->inputWidth(15)->vif('jump_id', $item['id']);
                    break;
            }
        }

        $form->item('config_id', '')->component(
            Input::make($config_id)->type('hidden')
        )->hideLabel();

        return $form;
    }
}
