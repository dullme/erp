<?php

namespace App\Admin\Controllers;

use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ProductController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '单品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

//        $grid->column('id', __('ID'));
        $grid->column('sku', __('SKU'));
        $grid->column('image', __('图片'))->image();
        $grid->column('description', __('描述'));
        $grid->column('length', __('长'));
        $grid->column('width', __('宽'));
        $grid->column('height', __('高'));
        $grid->column('volume', '体积')->display(function (){
            $volume = $this->length * $this->width * $this->height / 1000000;
            return round($volume, 2);
        });
        $grid->column('weight', __('毛重'));
        $grid->column('coefficient', '系数')->display(function (){
            return ($this->width + $this->height) * 2 + $this->length;
        });

        $grid->column('hq', 'HQ')->display(function ($hq){
            if($hq){
                return $hq." <i class='fa fa-check text-success'></i>";
            }

            $hq = 65 / ($this->length * $this->width * $this->height / 1000000);

            return round($hq, 0);
        });

        $grid->column('ddp', __('DDP'));
//        $grid->column('created_at', __('添加时间'));

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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('sku', __('SKU'));
        $show->field('length', __('长'));
        $show->field('width', __('宽'));
        $show->field('height', __('高'));
        $show->field('weight', __('毛重'));
        $show->field('ddp', __('DDP'));
        $show->field('image', __('图片'));
        $show->field('description', __('描述'));
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
        $form = new Form(new Product);

        $form->text('sku', __('SKU'));
        $form->number('length', __('长'));
        $form->number('width', __('宽'));
        $form->number('height', __('高'));
        $form->decimal('weight', __('毛重'));
        $form->number('ddp', __('DDP'));
        $form->number('hq', __('HQ'));
        $form->image('image', __('图片'));
        $form->text('description', __('描述'));
        $form->UEditor('content', __('详情'));

        return $form;
    }

    public function product()
    {
        $q = request()->input('q');
        $products = Product::where('sku', 'like', '%'.$q.'%')
            ->orWhere('description', 'like', '%'.$q.'%')
            ->select('id', 'sku as text', 'description', 'ddp', 'image')->get();

        return response()->json($products);
    }
}
