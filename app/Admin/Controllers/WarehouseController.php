<?php

namespace App\Admin\Controllers;

use App\Order;
use App\OrderBatch;
use App\Warehouse;
use Carbon\Carbon;
use DB;
use App\Product;
use Encore\Admin\Grid;

class WarehouseController extends ResponseController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '单品库存';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $grid->column('id', __('ID'));
        $grid->column('image', __('图片'))->display(function ($image) {
            return $image? 'thumb/' . $image : asset('images/default.png');
        })->image('', 50, 50);
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
        $grid->column('quantity_1', '中国仓')->display(function () {
            return $this->warehouses->where('status', 1)->sum('quantity');
        });

        $grid->column('quantity_2', '海上')->display(function () {
            return $this->warehouses->where('status', 2)->sum('quantity');
        });

        $grid->column('quantity_3', '美国仓')->display(function () {
            return $this->warehouses->where('status', 3)->sum('quantity');
        });

        $grid->column('quantity_4', '电商')->display(function () {
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

    public function first()
    {
        request()->validate([
            'order_id' => 'required',
            'entry_at' => 'required|date:Y-m-d',
        ], [
            'order_id.required' => '缺少必要参数',
            'entry_at.required' => '请选择入库时间',
            'entry_at.date'     => '时间格式错误',
        ]);

//        $file = request()->file('images');
        $productInfo = request()->input('product_info');
        $entry_at = Carbon::parse(request()->input('entry_at'));
        $productInfo = collect($productInfo)->where('deleted', 'false')->where('product_id', '!=', null);

        if ($productInfo->where('quantity', '<', 1)->count()) {
            return $this->setStatusCode(422)->responseError('单品数量必须大于0');
        }

        if ($productInfo->count() < 1) {
            return $this->setStatusCode(422)->responseError('必须添加单品');
        }

//        if ($file) {
//            $file = $this->saveAgreementFile($file);
//            $file = $file->pluck('path')->toArray();
//            $file = json_encode($file);
//        }
        $order = Order::with(['orderBatch'=>function($query){
            $query->where('status', 0);
        }])->findOrfail(request()->input('order_id'));
        $ids = $order->orderProduct->pluck('product_id')->toArray();
        $nowIds = $productInfo->pluck('product_id')->toArray();
        if(count(array_diff($nowIds, $ids)) > 0){
            return $this->setStatusCode(422)->responseError('无法入库，出错了！');
        }

        if($order->orderBatch->count()){
            return $this->setStatusCode(422)->responseError('无法入库，存在待审核的入库记录');
        }

        $productBatch = $productInfo->groupBy('product_id')->map(function ($item) use ($entry_at) {
            unset($item['deleted']);

            return [
                'product_id' => $item[0]['product_id'],
                'quantity'   => $item->sum('quantity'),
            ];
        })->toArray();

        OrderBatch::create([
            'order_id'      => $order->id,
            'status'        => 0,
            'batch'         => $order->batch + 1,
            'product_batch' => $productBatch,
            'entry_at'      => $entry_at->toDateTimeString(),
        ]);

        return $this->responseSuccess('添加成功');
    }


    public function canBox()
    {
        $q = request()->input('q');

        $products = Product::where('sku', 'like', '%' . $q . '%')
            ->orWhere('description', 'like', '%' . $q . '%')
            ->with('warehouses')
            ->select('id', 'sku', 'description', 'ddp', 'image')->get();

        $products = $products->map(function ($item) {
            $count = $item->warehouses->where('status', 1)->sum('quantity');
            $item['warehouses_count'] = $count;
            $item['text'] = $count;
            $item['disabled'] = !(boolean) $count;
            unset($item->warehouses);

            return $item;
        });

        return response()->json($products);
    }
}
