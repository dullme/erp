<?php

namespace App\Admin\Controllers;

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
        });

//        $grid->column('id', __('ID'));
        $grid->column('no', __('订单编号'))->display(function (){
            $url = url('/admin/orders/'.$this->id);
            return "<a href='{$url}'>{$this->no}</a>";
        });
        $grid->supplier()->name('供应商');
        $grid->column('signing_at', __('签订日'));
        $grid->column('product', __('SKU:数量'))->display(function ($product) {
            $products = Product::find(collect($product)->pluck('product_id'));
            $product = collect($product)->map(function ($item) use ($products) {
                $res = $products->where('id', $item['product_id'])->first();

                return "{$res->sku}:{$item['quantity']}";
            })->toArray();

            return implode("|", $product);
        });
//        $grid->column('remark', __('备注'));
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

        $order = Order::with('supplier')->findOrFail($id);
        $product_ids = collect($order->product)->pluck('product_id')->toArray();
        $real_product = Product::select('id', 'sku', 'image')->find($product_ids);
        $order->product = collect($order->product)->map(function ($item) use ($real_product) {
            $res = $real_product->where('id', $item['product_id'])->first()->toArray();

            return array_merge($res, [
                'quantity' => $item['quantity'],
                'price'    => bigNumber($item['price'])->getValue(),
                'total'    => bigNumber($item['quantity'] * $item['price'])->getValue()
            ]);
        });

        $product_batch_ids = collect($order->product_batch)->groupBy('product_id')->keys();
        $real_product_batch = Product::select('id', 'sku', 'image')->find($product_batch_ids);
        $order->product_batch = collect($order->product_batch)->map(function ($item) use ($real_product_batch) {
            $res = $real_product_batch->where('id', $item['product_id'])->first()->toArray();

            return array_merge($res, [
                'batch'      => $item['batch'],
                'entry_at' => $item['entry_at'],
                'quantity'   => $item['quantity'],
                'price'      => bigNumber($item['price'])->getValue(),
                'total'      => bigNumber($item['quantity'] * $item['price'])->getValue()
            ]);
        })->groupBy('batch')->reverse();

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

        return $content->header('订单管理')
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
        try {
            $productInfo = $productInfo->groupBy('product_id')->map(function ($item, $index) {
                $item->each(function ($price) use ($item) {
                    if ($price['price'] != $item[0]['price']) {
                        throw new \Exception('存在相同单品不同价格');
                    }
                });

                return [
                    'product_id' => $index,
                    'quantity'   => $item->sum('quantity'),
                    'price'      => round($item[0]['price'], 2),
                ];
            });

            Order::create([
                'no'          => request()->input('no'),
                'supplier_id' => request()->input('supplier_id'),
                'product'     => $productInfo->values(),
                'signing_at'  => request()->input('signing_at'),
                'remark'      => request()->input('remark'),
            ]);
            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }

    }
}
