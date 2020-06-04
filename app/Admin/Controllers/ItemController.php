<?php

namespace App\Admin\Controllers;

use App\Item;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ItemController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Item';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Item());
        $grid->model()->orderByDesc('id');
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', '名称');
        });

        $grid->actions(function ($action){
            $action->disableDelete();
        });

        $grid->column('name', __('名称'));
        $grid->column('unit', __('单位'));
        $grid->column('remark', __('备注'));
        $grid->column('created_at', __('添加时间'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Item::findOrFail($id));

        $show->field('name', __('名称'));
        $show->field('unit', __('单位'));
        $show->field('remark', __('备注'));
        $show->field('created_at', __('添加时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Item());

        $form->text('name', __('名称'))->creationRules(['required', "unique:items"])
            ->updateRules(['required', "unique:items,name,{{id}}"]);
        $form->select('unit', __('单位'))->options(getUnit());
        $form->textarea('remark', __('备注'));

        return $form;
    }

    public function itemList()
    {
        $q = request()->input('q');
        $items = Item::where('name', 'like', '%'.$q.'%')->get();

        return response()->json($items);
    }
}
