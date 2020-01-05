<?php

namespace App\Admin\Controllers;

use App\Order;
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

        if ($productInfo->where('price', '<=', 0)->count()) {
            return $this->setStatusCode(422)->responseError('单品价格必须大于0');
        }

        if ($productInfo->count() < 1) {
            return $this->setStatusCode(422)->responseError('必须添加单品');
        }

//        if ($file) {
//            $file = $this->saveAgreementFile($file);
//            $file = $file->pluck('path')->toArray();
//            $file = json_encode($file);
//        }
        $now = Carbon::now()->toDateTimeString();
        DB::beginTransaction(); //开启事务
        try {
            $order = Order::findOrfail(request()->input('order_id'));
            $batch = $order->batch + 1;

            $productBatch = $productInfo->map(function ($item) use ($batch, $entry_at) {
                $item['batch'] = $batch;
                $item['entry_at'] = $entry_at->toDateTimeString();
                unset($item['deleted']);

                return $item;
            })->toArray();

            $product_ids = $productInfo->pluck('product_id')->toArray();
            $real_product = Product::select('id', 'sku', 'image')->find($product_ids);

            $productInfo = $productInfo->groupBy('product_id')->map(function ($item, $index) use ($order, $now, $batch, $real_product, $entry_at) {
                $item->each(function ($price) use ($item) {
                    if ($price['price'] != $item[0]['price']) {
                        throw new \Exception('存在相同单品不同价格');
                    }
                });

                $real_pro = $real_product->where('id', $index)->first();

                return [
                    'order_id'     => $order->id,
                    'order_batch'  => $batch,
                    'batch_number' => "{$order->no}-{$real_pro->sku}-{$batch}-{$entry_at->weekOfYear}",
                    'product_id'   => $index,
                    'quantity'     => $item->sum('quantity'),
                    'status'       => 1,
                    'entry_at'     => $entry_at->toDateTimeString(),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            });

            $order->batch = $batch;
            if ($order->product_batch != null) {
                $order->product_batch = array_merge($order->product_batch, $productBatch);
            } else {
                $order->product_batch = $productBatch;
            }

            $order->save();

            Warehouse::insert($productInfo->toArray());

            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }
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
