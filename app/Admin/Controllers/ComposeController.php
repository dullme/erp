<?php

namespace App\Admin\Controllers;

use App\Compose;
use Encore\Admin\Admin;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ComposeController extends AdminController
{
    use AdminControllerTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '组合管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Compose);

        $grid->column('id', __('ID'));
        $grid->column('image', __('图片'))->image();
        $grid->column('name', __('组合名称'));
        $grid->column('asin', __('ASIN'))->display(function (){
            return "<a href='/admin/media?path=/{$this->asin}'>{$this->asin}</a>";
        });
        $grid->column('created_at', __('添加时间'));

        $grid->disableExport();

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
        $show = new Show(Compose::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('组合名称'));
        $show->field('asin', __('ASIN'));
        $show->field('image', __('图片'));
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
        $form = new Form(new Compose);

        $form->text('name', __('组合名称'))->rules('required');
        $form->text('asin', __('ASIN'))->rules('required');
        $form->image('image', __('图片'));

        $form->hasMany('composeProducts', '单品', function (Form\NestedForm $form) {
            $form->select('product_id', '单品')->options('/admin/api/product');
            $form->number('quantity', '数量');
        })->rules('required');

        return $form;
    }

    public function create(Content $content)
    {
        $this->loadVue();

        return $content->header('组合管理')
            ->description('创建')
            ->body('<compose></compose>');
    }
}
