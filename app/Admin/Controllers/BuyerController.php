<?php

namespace App\Admin\Controllers;

use App\Buyer;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class BuyerController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '采购商';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Buyer);
        $grid->model()->orderByDesc('id');
        $grid->disableExport();
        $grid->disableRowSelector();
        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', '名称');
            $filter->like('mobile', '电话');
        });

        $grid->column('name', __('名称'));
        $grid->column('english_name', __('英文名称'));
        $grid->column('contact_person', __('联系人'));
        $grid->column('position', __('职位'));
        $grid->column('mobile', __('手机号码'));
        $grid->column('tel', __('办公室电话'));
        $grid->column('fax', __('传真号码'));
        $grid->column('email', __('邮箱'));
        $grid->column('website', __('网站'));
        $grid->column('address', __('地址'));
        $grid->column('supply', __('境内货源地'));
        $grid->column('tax_id', __('税号'));
        $grid->column('bank', __('开户行'));
        $grid->column('bank_account', __('账号'));
        $grid->column('remark', __('备注'));
        $grid->column('register', __('登记'));
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
        $show = new Show(Buyer::findOrFail($id));

        $show->field('name', __('名称'));
        $show->field('english_name', __('英文名称'));
        $show->field('contact_person', __('联系人'));
        $show->field('position', __('职位'));
        $show->field('mobile', __('手机号码'));
        $show->field('tel', __('办公室电话'));
        $show->field('fax', __('传真号码'));
        $show->field('email', __('邮箱'));
        $show->field('website', __('网站'));
        $show->field('address', __('地址'));
        $show->field('supply', __('境内货源地'));
        $show->field('tax_id', __('税号'));
        $show->field('bank', __('开户行'));
        $show->field('bank_account', __('账号'));
        $show->field('register', __('备注'));
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
        $form = new Form(new Buyer);

        $form->text('name', __('名称'));
        $form->text('english_name', __('英文名称'));
        $form->text('contact_person', __('联系人'));
        $form->text('position', __('职位'));
        $form->mobile('mobile', __('手机号码'));
        $form->text('tel', __('办公室电话'));
        $form->text('fax', __('传真号码'));
        $form->email('email', __('邮箱'));
        $form->text('website', __('网站'));
        $form->text('address', __('地址'));
        $form->text('supply', __('境内货源地'));
        $form->text('tax_id', __('税号'));
        $form->text('bank', __('开户行'));
        $form->text('bank_account', __('账号'));
        $form->text('register', __('登记'));
        $form->textarea('remark', __('备注'));

        return $form;
    }

    public function getBuyer()
    {
        $q = request()->input('q');
        $products = Buyer::where('name', 'like', '%'.$q.'%')->get();

        return response()->json($products);
    }

    public function getBuyerSelect()
    {
        $q = request()->input('q');
        $products = Buyer::where('name', 'like', '%'.$q.'%')->select('id', 'name as text')->get();

        return response()->json($products);
    }
}
