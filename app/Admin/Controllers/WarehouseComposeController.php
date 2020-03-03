<?php

namespace App\Admin\Controllers;

use App\Compose;
use App\Warehouse;
use App\WarehouseCompany;
use DB;
use Encore\Admin\Form;
use Encore\Admin\Grid;

class WarehouseComposeController extends ResponseController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '组合库存';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        $company_id = request()->input('company_id');
        $grid = new Grid(new Compose);
        $grid->model()->orderByDesc('order')->orderByDesc('id');

        $grid->column('asin', __('ASKU'));
        $grid->column('image', '图片')->display(function ($image) {
            $data = [];
            if($image){
                foreach($image as $item){
                    $data[] = 'thumb/'.$item;
                    break;
                }
            }

            return count($data) ? $data : [asset('images/default.png')];
        })->image('', 50, 50);
        $grid->column('name', __('组合名称'))->display(function ($name) {
            $short = mb_substr($name,0, 20);

            return "<a href='/admin/composes/{$this->id}' data-toggle='tooltip' data-placement='top\' title='' data-original-title='{$name}'>{$short}</a>";
        });

        $grid->column('order', __('设置序号'))->editable();
        $grid->column('count', __('设置匹配数'))->editable();

        if(!is_null($company_id)){
            $warehouseCompany = WarehouseCompany::find($company_id);
            $grid->tools(function ($tools) use($warehouseCompany) {
                $tools->append("<span>仓储公司：{$warehouseCompany->name}</span>");
            });
        }

        $warehouseComposeC = new WarehouseCompose([3,4], $company_id);
        $warehouseCompose = $warehouseComposeC->getCompose();
        $warehouseComposeNowHave = $warehouseComposeC->getNowHave();

        $warehouseComposeWithSeaC = new WarehouseCompose([2,3,4], $company_id);
        $warehouseComposeWithSea = $warehouseComposeWithSeaC->getCompose();
        $warehouseComposeWithSeaNowHave = $warehouseComposeWithSeaC->getNowHave();

        $grid->column('quantity', '美仓库存')->display(function () use($warehouseCompose, $warehouseComposeNowHave) {

            $composeProducts = $this->composeProducts->map(function ($item){
                return [
                    'id' => $item['product_id'],
                    'sku' => $item['product']['sku'],
                    'quantity' => $item['quantity'],
                ];
            })->toArray();

            if($warehouseCompose[$this->id] >= $this->count){
                $color = 'success';
            }else if($warehouseCompose[$this->id] > 0){
                $color = 'info';
            }else{
                $color = 'warning';
            }

            $html = "<span style='display: inline-block' class='label label-{$color}'>{$warehouseCompose[$this->id]}</span><br/>";
            foreach ($composeProducts as $item){
                $count = isset($warehouseComposeNowHave[$item['id']]) ? $warehouseComposeNowHave[$item['id']] : 0;//剩余可匹配数

                $html .="<span style='display: inline-block' class='label label-default'>{$item['sku']} * {$item['quantity']}</span> <span style='display: inline-block' class='label label-default'>{$count}</span><br/>";
            }

            return $html;
        });

        $grid->column('quantityWithSea', '包括运输中的库存')->display(function () use($warehouseComposeWithSea, $warehouseComposeWithSeaNowHave) {

            $composeProducts = $this->composeProducts->map(function ($item){
                return [
                    'id' => $item['product_id'],
                    'sku' => $item['product']['sku'],
                    'quantity' => $item['quantity'],
                ];
            })->toArray();

            if($warehouseComposeWithSea[$this->id] >= $this->count){
                $color = 'success';
            }else if($warehouseComposeWithSea[$this->id] > 0){
                $color = 'info';
            }else{
                $color = 'warning';
            }

            $html = "<span style='display: inline-block' class='label label-{$color}'>{$warehouseComposeWithSea[$this->id]}</span><br/>";
            foreach ($composeProducts as $item){
                $count = isset($warehouseComposeWithSeaNowHave[$item['id']]) ? $warehouseComposeWithSeaNowHave[$item['id']] : 0;//剩余可匹配数

                $html .="<span style='display: inline-block' class='label label-default'>{$item['sku']} * {$item['quantity']}</span> <span style='display: inline-block' class='label label-default'>{$count}</span><br/>";
            }

            return $html;
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->in('id', 'ASKU')->multipleSelect('api/compose-select');
        });

        $grid->disableExport();
        $grid->disableActions();
        $grid->disableColumnSelector();
        $grid->disableCreateButton();
        $grid->disableRowSelector();

        return $grid;
    }


    protected function form()
    {
        $form = new Form(new Compose);

        $form->text('name', __('组合名称'))->rules('required');
        $form->text('asin', __('ASIN'))->rules('required');
        $form->text('hq', __('HQ'));
        $form->number('count', __('设置匹配数'));
        $form->number('order', __('序号'))->rules('integer');
        $form->multipleImage('image', __('图片'))->name(function ($file) {
            $img = Image::make($file)->widen(300, function ($constraint) {
                $constraint->upsize();
            });
            $path = md5(uniqid()).'.'.$file->guessExtension();
            $img->save('uploads/thumb/images/'.$path);
            return $path;
        })->removable();
        $form->UEditor('content', __('详情'));

        return $form;
    }

}
