<?php

namespace App\Admin\Controllers;

use App\Compose;
use App\Warehouse;
use App\WarehouseCompany;
use DB;
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
        $grid->model()->orderByDesc('id');

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

        $grid->column('count', '设置匹配数');

        $grid->column('quantity', '美仓库存')->display(function () use($warehouseCompose, $warehouseComposeNowHave) {
            $composeProducts = $this->composeProducts->pluck('quantity', 'product_id')->toArray();
            $count=[];
            foreach ($composeProducts as $key=>$item){
                if(isset($warehouseComposeNowHave[$key])){
                    $count[] = intval($warehouseComposeNowHave[$key]/$item);
                }else{
                    $count[] = 0;
                }
            }

            return $warehouseCompose[$this->id].'-'.min($count);
        });

        $grid->column('quantityWithSea', '包括运输中的库存')->display(function () use($warehouseComposeWithSea, $warehouseComposeWithSeaNowHave) {
            $composeProducts = $this->composeProducts->pluck('quantity', 'product_id')->toArray();
            $count=[];
            foreach ($composeProducts as $key=>$item){
                if(isset($warehouseComposeWithSeaNowHave[$key])){
                    $count[] = intval($warehouseComposeWithSeaNowHave[$key]/$item);
                }else{
                    $count[] = 0;
                }
            }

            return $warehouseComposeWithSea[$this->id].'-'.min($count);
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
}
