<?php

namespace App\Admin\Controllers;

use App\ForwardingCompany;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ForwardingCompanyController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '货代公司';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ForwardingCompany);
        $grid->model()->orderByDesc('id');
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->column('name', __('名称'));
        $grid->column('mobile', __('电话'));
        $grid->column('address', __('地址'));
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
        $show = new Show(ForwardingCompany::findOrFail($id));

        $show->field('name', __('名称'));
        $show->field('mobile', __('电话'));
        $show->field('address', __('地址'));
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
        $form = new Form(new ForwardingCompany);

        $form->text('name', __('名称'));
        $form->mobile('mobile', __('电话'));
        $form->text('address', __('地址'));

        return $form;
    }

    public function forwardingCompany()
    {
        $q = request()->input('q');
        $forwardingCompany = ForwardingCompany::where('name', 'like', '%'.$q.'%')->get();

        return response()->json($forwardingCompany);
    }
}
