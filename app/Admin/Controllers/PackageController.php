<?php

namespace App\Admin\Controllers;

use App\ForwardingCompany;
use App\Package;
use App\Product;
use App\Warehouse;
use Carbon\Carbon;
use DB;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class PackageController extends ResponseController
{

    use AdminControllerTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '装箱运送';

    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid())
            ->body(view('add_contact'));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Package);
        $grid->model()->orderByDesc('id');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->equal('lading_number', '提单号');
            $filter->equal('container_number', '集装箱号');
            $filter->equal('seal_number', '铅封号');
            $forwardingCompany = ForwardingCompany::pluck('name', 'id');
            $filter->equal('forwarding_company_id', '货代公司')->select($forwardingCompany);
        });

        $grid->column('lading_number', __('提单号'))->display(function (){
            $url = url('/admin/packages/'.$this->id);
            return "<a href='{$url}'>{$this->lading_number}</a>";
        });
        $grid->column('container_number', __('集装箱号'))->display(function (){
            $url = url('/admin/packages/'.$this->id);
            return "<a href='{$url}'>{$this->container_number}</a>";
        });
        $grid->column('seal_number', __('铅封号'))->display(function (){
            $url = url('/admin/packages/'.$this->id);
            return "<a href='{$url}'>{$this->seal_number}</a>";
        });
        $grid->column('packaged_at', __('装箱时间'));
        $grid->forwardingCompany()->name('货代公司');
        $grid->column('product', __('SKU:数量'))->display(function ($product) {
            $products = Product::find(collect($product)->pluck('product_id'));
            $product = collect($product)->map(function ($item) use ($products) {
                $res = $products->where('id', $item['product_id'])->first();

                return "{$res->sku}:{$item['quantity']}";
            })->toArray();

            return implode("|", $product);
        });
        $grid->column('in', '入库')->display(function (){
            if($this->warehouse->where('status', 2)->count()){
                return "<a style='cursor: pointer' data-toggle='modal' data-target='#add_contact' title='添加联系人' onclick=\"addContact({$this->id})\"><i class='fa fa-balance-scale'></i></a>";
            }else{
                return "<i class='fa fa-check text-success'></i>";
            }
        });
//        $grid->column('remark', __('Remark'));
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));

        $grid->disableRowSelector();
        $grid->disableExport();

        $grid->actions(function ($actions) {
            $actions->disableEdit();
            $actions->disableDelete();
        });

        $grid->disableActions();

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
        $package = Package::with([
            'warehouse' => function ($query) {
                $query->whereIn('status', [2,3,4])->with('product', 'warehouseCompany');
            }
        ])->find($id);

        $res = $package->warehouse->groupBy('product_id')->map(function ($item) {
            $first = $item->first();
            return array_merge($first->product->toArray(), [
                'quantity' => $item->sum('quantity'),
                'status' => $first->status,
                'warehouse_company' => optional($first->warehouseCompany)->name,
                'batch_number' => $item,
            ]);
        })->values();

        $package->offsetUnset('warehouse');
        $package->setAttribute('warehouse', $res);

        return view('package', compact('package'));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Package);

        $form->number('forwarding_company_id', __('Forwarding company id'));
        $form->text('lading_number', __('Lading number'));
        $form->text('container_number', __('Container number'));
        $form->text('seal_number', __('Seal number'));
        $form->textarea('product', __('Product'));
        $form->text('packaged_at', __('Packaged at'));
        $form->text('remark', __('Remark'));

        return $form;
    }

    public function create(Content $content)
    {
        $this->loadVue();

        return $content->header('装箱运送')
            ->description('创建')
            ->body('<package></package>');
    }

    public function store()
    {
        $request = request()->validate([
            'lading_number'         => 'required|unique:packages,lading_number',
            'container_number'      => 'required|unique:packages,container_number',
            'seal_number'           => 'required|unique:packages,seal_number',
            'forwarding_company_id' => 'required',
            'packaged_at'           => 'required|date:Y-m-d',
            'remark'                => 'nullable',
        ], [
            'lading_number.required'         => '请输入提单号',
            'lading_number.unique'           => '该提单号已存在',
            'container_number.required'      => '请输入集装箱号',
            'container_number.unique'        => '该集装箱号已存在',
            'seal_number.required'           => '请输入铅封号',
            'seal_number.unique'             => '该铅封号已存在',
            'forwarding_company_id.required' => '请选择供货代公司',
            'packaged_at.required'           => '请选择装箱日',
            'packaged_at.date'               => '装箱日格式错误',
        ]);

//        $file = request()->file('images');
        $productInfo = request()->input('product_info');
        $packaged_at = Carbon::parse(request()->input('packaged_at'));
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

        DB::beginTransaction(); //开启事务
        try {
            $productInfo = $productInfo->groupBy('product_id')->map(function ($item, $index) use ($packaged_at) {

                return [
                    'product_id'  => $index,
                    'quantity'    => $item->sum('quantity'),
                    'packaged_at' => $packaged_at->toDateTimeString(),
                ];
            });

            $warehouse = Warehouse::where('status', 1)->whereIn('product_id', $productInfo->pluck('product_id'))->get();
            $warehouse = $warehouse->groupBy('product_id');

            $request['product'] = $productInfo->values();
            $package = Package::create($request);

            $productInfo->map(function ($item) use ($warehouse, $package) {
                $quantity = collect($warehouse[$item['product_id']])->sum('quantity');

                if ($item['quantity'] < $quantity) { //库存充足

                    $now_quantity = $item['quantity'];
                    foreach ($warehouse[$item['product_id']]->toArray() as $oneWarehouse) {
                        if ($oneWarehouse['quantity'] < $now_quantity) {
                            //如果某批次不足当前装柜所需则全部改为海上仓
                            Warehouse::where('id', $oneWarehouse['id'])->update([
                                "status"     => 2,
                                "package_id" => $package->id,
                            ]);
                            $now_quantity -= $oneWarehouse['quantity']; //减少总需要的货物
                        } else {
                            if ($oneWarehouse['quantity'] == $now_quantity) {
                                //如果当前和所需货物刚好一致则更新
                                Warehouse::where('id', $oneWarehouse['id'])->update([
                                    "status"     => 2,
                                    "package_id" => $package->id,
                                ]);
                            } else { //大于本次装柜所需
                                Warehouse::where('id', $oneWarehouse['id'])->update([
                                    'quantity' => $oneWarehouse['quantity'] - $item['quantity']
                                ]);//减少原来的数量
                                Warehouse::create([
                                    "order_id"     => $oneWarehouse['order_id'],
                                    "package_id"   => $package->id,
                                    "order_batch"  => $oneWarehouse['order_batch'],
                                    "batch_number" => $oneWarehouse['batch_number'],
                                    "product_id"   => $oneWarehouse['product_id'],
                                    "status"       => 2,
                                    "quantity"     => $item['quantity'],
                                    "entry_at"     => $oneWarehouse['entry_at'],
                                ]); //新增海上仓的数量
                            }
                            break; //结束循环
                        }
                    }
                } else if ($item['quantity'] == $quantity) { //全部一致
                    Warehouse::whereIn('id', $warehouse[$item['product_id']]->pluck('id'))->update([
                        "status"     => 2,
                        "package_id" => $package->id,
                    ]);
                } else {
                    throw new \Exception('库存不足');
                }
            });

            DB::commit();   //保存

            return $this->responseSuccess('装柜成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }
    }

    public function getPackageInfo($id)
    {
        $package = Package::with([
            'warehouse' => function ($query) {
                $query->where('status', 2)->with('product');
            },
            'forwardingCompany:id,name'
        ])->find($id);

        $res = $package->warehouse->groupBy('product_id')->map(function ($item) {

            return array_merge($item->first()->product->toArray(), [
                'quantity' => $item->sum('quantity'),
            ]);
        })->values();

        $package->offsetUnset('warehouse');
        $package->setAttribute('warehouse', $res);

        return response()->json($package);
    }

    public function packageIn(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'company' => 'required',
        ]);

        $warehouse = Warehouse::where([
            'status'=> 2,
            'package_id' => $data['id']
        ])->update([
            'status' => 3,
            'warehouse_company_id' => $data['company']
        ]);

        return response()->json($warehouse);
    }
}
