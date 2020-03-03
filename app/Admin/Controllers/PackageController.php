<?php

namespace App\Admin\Controllers;

use App\Exports\PackagesExport;
use App\ForwardingCompany;
use App\Package;
use App\Product;
use App\Warehouse;
use Carbon\Carbon;
use DB;
use Excel;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $grid->column('lading_number', __('提单号'))->display(function () {
            $url = url('/admin/packages/' . $this->id);
            $dir = "/admin/media?path=/packages/".$this->lading_number;
            return "<a href='{$dir}'><i class='fa fa-file'></i></a> <a href='{$url}'>{$this->lading_number}</a>";
        });

        $grid->column('container_number', __('集装箱号'))->display(function () {
            $url = url('/admin/packages/' . $this->id);

            return "<a href='{$url}'>{$this->container_number}</a>";
        });
        $grid->column('seal_number', __('铅封号'))->display(function () {
            $url = url('/admin/packages/' . $this->id);

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

            $long = implode("｜", $product);
            $mark = count($product) > 3 ? ' ...' : '';
            $short = implode("｜", array_slice($product, 0, 3)) . $mark;

            return "<span data-toggle='tooltip' data-placement='top\' title='' data-original-title='{$long}'>{$short}</span>";
        });
        $grid->column('status', '状态')->display(function ($status) {
            if ($status == 1) {
                return "<a style='cursor: pointer' data-toggle='modal' data-target='#add_contact' title='' onclick=\"addContact({$this->id})\"><i class='fa fa-balance-scale'></i></a>";
            } else if ($status == 2) {
                return '<span class="label label-success">已入库</span>';
            } else {
                return '<span class="label label-danger">待审核</span>';
            }
        });

        $grid->column('report', '是否报告')->display(function ($status) {
            if ($status) {
                return '<span class="label label-success">是</span>';
            }

            return '<span class="label label-danger">否</span>';
        });

//        $grid->column('remark', __('Remark'));
//        $grid->column('created_at', __('Created at'));
//        $grid->column('updated_at', __('Updated at'));

        $grid->disableRowSelector();
        $grid->disableExport();

        $grid->actions(function ($actions) {
            if (in_array($this->row->status, [1, 2])) {
                $actions->disableEdit();
                $actions->disableDelete();
            }
        });

//        $grid->disableActions();

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
                $query->whereIn('status', [2, 3, 4])->with('product', 'warehouseCompany');
            },
            'forwardingCompany',
            'buyer',
            'customer',
            'warehouseCompany',
        ])->find($id);

        $res = $package->warehouse->groupBy('product_id')->map(function ($item) {
            $first = $item->first();

            return array_merge($first->product->toArray(), [
                'quantity'          => $item->sum('quantity'),
                'status'            => $first->status,
                'warehouse_company' => optional($first->warehouseCompany)->name,
                'batch_number'      => $item,
            ]);
        })->values();

        $package->offsetUnset('warehouse');
        $package->setAttribute('warehouse', $res);

        $products = Product::whereIn('id', collect($package->product)->pluck('product_id')->toArray())->select('id', 'sku', 'image')->get();

        $package->product = collect($package->product)->map(function ($item) use ($products) {
            $product = $products->where('id', $item['product_id'])->first()->toArray();
            $product['quantity'] = $item['quantity'];
            $product['packaged_at'] = $item['packaged_at'];

            return $product;
        });

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

        $form->text('lading_number', __('提单号'))->required();
        $form->text('agreement_no', __('合同号'))->required();
        $form->text('container_number', __('集装箱号'));
        $form->text('seal_number', __('铅封号'));
        $form->select('forwarding_company_id', __('货代'))->options('/admin/api/forwarding-company-select')->required();
        $form->date('packaged_at', __('发货日'))->default(date('Y-m-d'))->required();
        $form->select('ship_port', __('发货港'))->options('/admin/api/port-select1')->required();
        $form->date('departure_at', __('预计离港时间'))->default(date('Y-m-d'))->required();
        $form->select('arrival_port', __('到货港'))->options('/admin/api/port-select2')->required();
        $form->date('arrival_at', __('预计到港时间'))->default(date('Y-m-d'))->required();
        $form->date('entry_at', __('预计入仓时间'))->default(date('Y-m-d'))->required();
        $form->select('buyer_id', __('出口商'))->options('/admin/api/buyer-select')->required();
        $form->select('customer_id', __('进口商'))->options('/admin/api/customer-select')->required();
        $form->select('warehouse_company_id', __('仓储公司'))->options('/admin/api/warehouse-company')->required();
        $form->textarea('remark', __('备注'));
        $form->switch('report', __('是否报告'))->states([
            'on'  => ['value' => 1, 'text' => '是'],
            'off' => ['value' => 0, 'text' => '否'],
        ]);

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
            'agreement_no'          => 'required',
            'container_number'      => 'nullable',
            'seal_number'           => 'nullable',
            'ship_port'             => 'required',
            'arrival_port'          => 'required',
            'forwarding_company_id' => 'required',
            'warehouse_company_id'  => 'required',
            'buyer_id'              => 'required',
            'customer_id'           => 'required',
            'packaged_at'           => 'required|date:Y-m-d',
            'departure_at'          => 'required|date:Y-m-d',
            'arrival_at'            => 'required|date:Y-m-d',
            'entry_at'              => 'required|date:Y-m-d',
            'remark'                => 'nullable',
        ], [
            'agreement_no.required'          => '请输入合同号',
            'lading_number.required'         => '请输入提单号',
            'lading_number.unique'           => '该提单号已存在',
            'forwarding_company_id.required' => '请选择供货代公司',
            'buyer_id.required'              => '请选择出口商',
            'customer_id.required'           => '请选择进口商',
            'warehouse_company_id.required'  => '请选择仓储公司',
            'ship_port.required'             => '请选择发货港',
            'arrival_port.required'          => '请选择到货港',
            'packaged_at.required'           => '请选择发货日',
            'packaged_at.date'               => '发货日格式错误',
            'departure_at.required'          => '请选择离港时间',
            'departure_at.date'              => '离港时间格式错误',
            'arrival_at.required'            => '请选择到港时间',
            'arrival_at.date'                => '到港时间格式错误',
            'entry_at.required'              => '请选择入仓时间',
            'entry_at.date'                  => '入仓时间格式错误',
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

            $request['product'] = $productInfo->values();
            Storage::makeDirectory('public/packages/' . request()->input('lading_number'));
            $package = Package::create($request);

            $warehouse = Warehouse::where('status', 1)->whereIn('product_id', $productInfo->pluck('product_id'))->get();
            $warehouse = $warehouse->groupBy('product_id');

            $productInfo->map(function ($item) use ($warehouse, $package) {
                $quantity = collect($warehouse[$item['product_id']])->sum('quantity');

                if ($item['quantity'] < $quantity) { //库存充足

//                    $now_quantity = $item['quantity'];
//                    foreach ($warehouse[$item['product_id']]->toArray() as $oneWarehouse) {
//                        if ($oneWarehouse['quantity'] < $now_quantity) {
//                            //如果某批次不足当前装柜所需则全部改为海上仓
//                            Warehouse::where('id', $oneWarehouse['id'])->update([
//                                "status"     => 2,
//                                "package_id" => $package->id,
//                            ]);
//                            $now_quantity -= $oneWarehouse['quantity']; //减少总需要的货物
//                        } else {
//                            if ($oneWarehouse['quantity'] == $now_quantity) {
//                                //如果当前和所需货物刚好一致则更新
//                                Warehouse::where('id', $oneWarehouse['id'])->update([
//                                    "status"     => 2,
//                                    "package_id" => $package->id,
//                                ]);
//                            } else { //大于本次装柜所需
//                                Warehouse::where('id', $oneWarehouse['id'])->update([
//                                    'quantity' => $oneWarehouse['quantity'] - $now_quantity
//                                ]);//减少原来的数量
//                                Warehouse::create([
//                                    "order_id"     => $oneWarehouse['order_id'],
//                                    "package_id"   => $package->id,
//                                    "order_batch"  => $oneWarehouse['order_batch'],
//                                    "batch_number" => $oneWarehouse['batch_number'],
//                                    "product_id"   => $oneWarehouse['product_id'],
//                                    "status"       => 2,
//                                    "quantity"     => $now_quantity,
//                                    "entry_at"     => $oneWarehouse['entry_at'],
//                                ]); //新增海上仓的数量
//                            }
//                            break; //结束循环
//                        }
//                    }
                } else if ($item['quantity'] == $quantity) { //全部一致
//                    Warehouse::whereIn('id', $warehouse[$item['product_id']]->pluck('id'))->update([
//                        "status"     => 2,
//                        "package_id" => $package->id,
//                    ]);
                } else {
                    throw new \Exception('库存不足');
                }
            });

            DB::commit();   //保存

            return $this->responseSuccess('保存成功');

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
            'forwardingCompany:id,name',
            'warehouseCompany:id,name'
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
            'id'      => 'required',
            'company' => 'required',
        ]);
        DB::beginTransaction(); //开启事务
        try {

            $package = Package::findOrFail($data['id']);

            if ($package->status != 1) {
                throw new \Exception('状态错误');
            }

            $package->status = 2;
            $package->checkin_at = Carbon::now();
            $package->save();

            $warehouse = Warehouse::where([
                'status'     => 2,
                'package_id' => $data['id']
            ])->update([
                'status'               => 3,
                'warehouse_company_id' => $data['company']
            ]);

            if ($warehouse == 0) {
                throw new \Exception('入库失败！');
            }
            DB::commit();   //保存

            return $this->responseSuccess($warehouse);
        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }
    }

    public function packageReview($id)
    {
        DB::beginTransaction(); //开启事务
        try {
            $package = Package::findOrFail($id);

            if ($package->status != 0) {
                throw new \Exception('状态错误');
            }

            $package->status = 1;
            $package->save();

            $productInfo = collect($package->product);

            $warehouse = Warehouse::where('status', 1)->whereIn('product_id', $productInfo->pluck('product_id'))->get();
            $warehouse = $warehouse->groupBy('product_id');

            $productInfo->map(function ($item) use ($warehouse, $package) {
                $quantity = collect($warehouse[$item['product_id']])->sum('quantity');

                if ($item['quantity'] < $quantity) { //库存充足

                    $now_quantity = $item['quantity'];
                    foreach ($warehouse[$item['product_id']]->toArray() as $oneWarehouse) {
                        if ($oneWarehouse['quantity'] < $now_quantity) {
                            //如果某批次不足当前装柜所需则全部改为海上仓
                            Warehouse::where('id', $oneWarehouse['id'])->update([
                                "status"               => 2,
                                "package_id"           => $package->id,
                                'warehouse_company_id' => $package->warehouse_company_id
                            ]);
                            $now_quantity -= $oneWarehouse['quantity']; //减少总需要的货物
                        } else {
                            if ($oneWarehouse['quantity'] == $now_quantity) {
                                //如果当前和所需货物刚好一致则更新
                                Warehouse::where('id', $oneWarehouse['id'])->update([
                                    "status"               => 2,
                                    "package_id"           => $package->id,
                                    'warehouse_company_id' => $package->warehouse_company_id
                                ]);
                            } else { //大于本次装柜所需
                                Warehouse::where('id', $oneWarehouse['id'])->update([
                                    'quantity' => $oneWarehouse['quantity'] - $now_quantity
                                ]);//减少原来的数量
                                Warehouse::create([
                                    "order_id"             => $oneWarehouse['order_id'],
                                    "package_id"           => $package->id,
                                    'warehouse_company_id' => $package->warehouse_company_id,
                                    "order_batch"          => $oneWarehouse['order_batch'],
                                    "batch_number"         => $oneWarehouse['batch_number'],
                                    "product_id"           => $oneWarehouse['product_id'],
                                    "status"               => 2,
                                    "quantity"             => $now_quantity,
                                    "entry_at"             => $oneWarehouse['entry_at'],
                                ]); //新增海上仓的数量
                            }
                            break; //结束循环
                        }
                    }
                } else if ($item['quantity'] == $quantity) { //全部一致
                    Warehouse::whereIn('id', $warehouse[$item['product_id']]->pluck('id'))->update([
                        "status"               => 2,
                        "package_id"           => $package->id,
                        'warehouse_company_id' => $package->warehouse_company_id
                    ]);
                } else {
                    throw new \Exception('库存不足');
                }
            });

            DB::commit();   //保存

            return $this->responseSuccess(true);
        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }
    }

    public function downloadPackage($id)
    {
        $package = Package::with([
            'warehouse' => function ($query) {
                $query->whereIn('status', [2, 3, 4])->with('product', 'warehouseCompany');
            },
            'forwardingCompany',
            'buyer',
            'customer',
            'warehouseCompany',
        ])->find($id);

        $res = $package->warehouse->groupBy('product_id')->map(function ($item) {
            $first = $item->first();

            return array_merge($first->product->toArray(), [
                'quantity'          => $item->sum('quantity'),
                'status'            => $first->status,
                'warehouse_company' => optional($first->warehouseCompany)->name,
                'batch_number'      => $item,
            ]);
        })->values();

        $package->offsetUnset('warehouse');
        $package->setAttribute('warehouse', $res);

        $products = Product::whereIn('id', collect($package->product)->pluck('product_id')->toArray())->select('id', 'sku', 'image')->get();

        $data = [
            'forwarding_company' => $package->forwardingCompany->name,//货运公司
            'lading_number' => $package->lading_number,//提单号
            'container_number' => $package->container_number,//集装箱号
            'seal_number' => $package->seal_number,//铅封号
//            'packaged_at' => Carbon::parse($package->packaged_at)->toDateString(),//发货日
            'arrival_at' => Carbon::parse($package->arrival_at)->toDateString(),//到港时间
            'entry_at' => Carbon::parse($package->entry_at)->toDateString(),//预计入仓时间
            'checkin_at' => Carbon::parse($package->checkin_at)->toDateString(),//实际入仓时间
        ];

        $package->product = collect($package->product)->map(function ($item) use ($products, $data) {
            $product = $products->where('id', $item['product_id'])->first()->toArray();
            $product['quantity'] = $item['quantity'];
            $product['packaged_at'] = $item['packaged_at'];

            return array_merge($data, [
                'sku' => $product['sku'],
                'quantity' => $item['quantity'],
                'packaged_at' => Carbon::parse($item['packaged_at'])->toDateString(),//发货日
            ]);
        });

        return Excel::download(
            new PackagesExport($package->product, $package->lading_number),
            "{$package->lading_number}_发货清单.xlsx"   //导出的文件名
        );
    }
}
