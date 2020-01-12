<?php

namespace App\Admin\Controllers;

use App\OrderBatch;
use App\OrderProduct;
use App\Product;
use App\Supplier;
use DB;
use App\Order;
use App\Warehouse;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class OrderController extends ResponseController
{

    use AdminControllerTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订购入库';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->model()->orderByDesc('id');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('no', '订单编号');
            $suppliers = Supplier::pluck('name', 'id');
            $filter->equal('supplier_id', '供应商')->select($suppliers);
            $filter->equal('status', '状态')->select([0=>'进行中', 1=>'已完成']);
        });

//        $grid->column('id', __('ID'));
        $grid->column('no', __('订单编号'))->display(function () {
            $url = url('/admin/orders/' . $this->id);

            return "<a href='{$url}'>{$this->no}</a>";
        });
        $grid->supplier()->name('供应商');
        $grid->column('signing_at', __('签订日'));
        $grid->column('product', __('SKU:数量'))->display(function () {
            $products = Product::find($this->orderProduct->pluck('product_id'));
            $product = $this->orderProduct->map(function ($item) use ($products) {
                $res = $products->where('id', $item['product_id'])->first();

                return "{$res->sku}:{$item['quantity']}";
            })->toArray();

            return implode("|", $product);
        });
        $grid->column('status', __('状态'))->display(function ($status){
            if($status == 0){
                return "<span class='label label-danger'>进行中</span>";
            }
            return "<span class='label label-success' data-toggle=\"tooltip\" data-placement=\"top\" title=\"\" data-original-title='{$this->finished_at}'>已完成</span>";
        });
//        $grid->column('created_at', __('添加时间'));
//        $grid->column('updated_at', __('Updated at'));

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });

        $grid->disableActions();

        $grid->disableRowSelector();
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
        $this->loadVue();

        $order = Order::with('supplier', 'orderProduct.product:id,sku,image', 'orderBatch')->findOrFail($id);

        if($order->orderBatch->count()){
            $product_batch_ids = $order->orderBatch->pluck('product_batch')->flatten(1)->pluck('product_id')->unique()->values()->toArray();
            $real_product_batch = Product::select('id', 'sku', 'image')->find($product_batch_ids);

            $order->orderBatch->map(function ($item) use ($real_product_batch) {
                $item['product_batch'] = collect($item->product_batch)->map(function ($product) use ($real_product_batch){

                    return array_merge($real_product_batch->where('id', $product['product_id'])->first()->toArray(),$product);
                });

                return $item;
            });
        }

        return view('order', compact('order'));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', __('订单编号'));
        $form->text('batch', __('生产批号'));
        $form->text('remark', __('备注'));

        return $form;
    }

    public function create(Content $content)
    {
        $this->loadVue();

        return $content->header('订购入库')
            ->description('创建')
            ->body('<order></order>');
    }

    public function store()
    {
        request()->validate([
            'no'          => 'required|unique:orders,no',
            'supplier_id' => 'required',
            'signing_at'  => 'required|date:Y-m-d',
        ], [
            'no.required'          => '请输入订单编号',
            'no.unique'            => '该订单编号已存在',
            'supplier_id.required' => '请选择供应商',
            'signing_at.required'  => '请选择签订日',
            'signing_at.date'      => '签订日格式错误',
        ]);

//        $file = request()->file('images');
        $productInfo = request()->input('product_info');
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

        DB::beginTransaction(); //开启事务
        $now = Carbon::now()->toDateTimeString();
        try {
            $order = Order::create([
                'no'          => request()->input('no'),
                'supplier_id' => request()->input('supplier_id'),
                'signing_at'  => request()->input('signing_at'),
                'remark'      => request()->input('remark'),
            ]);

            $productInfo = $productInfo->map(function ($item) use ($order, $now) {

                return [
                    'order_id'      => $order->id,
                    'product_id'    => $item['product_id'],
                    'quantity'      => $item['quantity'],
                    'price'         => $item['price'],
                    'inspection_at' => $item['inspection_at'],
                    'remark'        => $item['remark'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            });

            OrderProduct::insert($productInfo->toArray());

            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }

    }

    public function deleteOrder($id)
    {
        $order = Order::with('orderBatch')->findOrFail($id);

        if($order->orderBatch->count()){
            return $this->setStatusCode(422)->responseError('存在入库记录无法删除');
        }

        $order->delete();

        return $this->responseSuccess(true);
    }


    public function deleteOrderBatch($id)
    {
        $orderBatch = OrderBatch::where('status', 0)->findOrFail($id);
        $orderBatch->delete();

        return $this->responseSuccess(true);
    }

    public function confirmOrderBatch($id)
    {
        DB::beginTransaction(); //开启事务
        $now = Carbon::now()->toDateTimeString();
        try {

            $orderBatch = OrderBatch::where('status', 0)->findOrFail($id);
            $orderBatch->status = 1;
            $orderBatch->save();

            $order = Order::find($orderBatch->order_id);
            $order->batch = ++$order->batch;
            $order->save();

            $products = Product::whereIn('id', collect($orderBatch->product_batch)->groupBy('product_id')->keys()->toArray())->pluck('sku', 'id');

            $productInfo = collect($orderBatch->product_batch)->map(function ($item) use($order, $products, $orderBatch, $now){
                $sku = $products[$item['product_id']];
                $entry_at = Carbon::parse($orderBatch->entry_at);
                return [
                    'order_id'     => $order->id,
                    'order_batch'  => $order->batch,
                    'batch_number' => "{$order->no}-{$sku}-{$order->batch}-{$entry_at->weekOfYear}",
                    'product_id'   => $item['product_id'],
                    'quantity'     => $item['quantity'],
                    'status'       => 1,
                    'entry_at'     => $entry_at->toDateTimeString(),
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ];
            });

            Warehouse::insert($productInfo->toArray());

            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }
    }

    public function finishOrder($id)
    {
        $order = Order::with('orderBatch')->findOrFail($id);

        if($order->status != 0){
            return $this->setStatusCode(422)->responseError('订单状态错误');
        }

        if($order->orderBatch->count() == 0){
            return $this->setStatusCode(422)->responseError('该订单还未入库无法完结');
        }

        if($order->orderBatch->where('status', 0)->count()){
            return $this->setStatusCode(422)->responseError('存在待审核的订单');
        }

        $order->status = 1;
        $order->finished_at = Carbon::now();
        $order->save();

        return $this->responseSuccess(true);
    }
}
