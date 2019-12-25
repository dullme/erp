<?php

namespace App\Admin\Controllers;

use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class WarehouseController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '库存管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->column('id', __('ID'));
        $grid->column('image', __('图片'))->image();
        $grid->column('description', __('描述'));
        $grid->column('sku', __('SKU'));
//        $grid->column('volume', '体积')->display(function (){
//            return $this->length * $this->width * $this->height / 1000000;
//        });
//        $grid->column('weight', __('毛重'));
//        $grid->column('coefficient', '系数')->display(function (){
//            return ($this->width + $this->height) * 2 + $this->length;
//        });
//
//        $grid->column('hq', 'HQ')->display(function (){
//            return 65 / ($this->length * $this->width * $this->height / 1000000);
//        });

        $grid->column('ddp', __('DDP'));
        $grid->column('quantity_1', '中国仓')->display(function (){
            return $this->warehouses->where('status', 1)->sum('quantity');
        });

        $grid->column('quantity_2', '海上')->display(function (){
            return $this->warehouses->where('status', 2)->sum('quantity');
        });

        $grid->column('quantity_3', '美国仓')->display(function (){
            return $this->warehouses->where('status', 3)->sum('quantity');
        });

        $grid->column('quantity_4', '电商')->display(function (){
            return $this->warehouses->where('status', 4)->sum('quantity');
        });
//        $grid->column('created_at', __('添加时间'));

        $grid->disableExport();
        $grid->disableActions();
        $grid->disableColumnSelector();
        $grid->disableCreateButton();
        $grid->disableRowSelector();

        return $grid;
    }
}
