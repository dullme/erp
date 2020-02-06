<?php

namespace App\Admin\Controllers;

use App\Product;
use DB;
use Image;
use Illuminate\Support\Facades\Storage;
use Session;
use App\Compose;
use App\ComposeProduct;
use App\Services\FileService;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ComposeController extends ResponseController
{

    use AdminControllerTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '组合管理';

    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid())
            ->body(view('change_hq'));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Compose);
        $grid->model()->orderByDesc('order');

        $session_hq = Session::get('hq', config('hq'));

//        $grid->column('id', __('ID'));
        $grid->column('asin', __('ASKU'))->display(function () {
            return "<a href='/admin/media?path=/composes/{$this->asin}'>{$this->asin}</a>";
        });
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
        $grid->column('box', '箱数')->display(function () {
            return $this->composeProducts->sum('quantity');
        });

        $grid->column('hq', '箱规'.getHq($session_hq))->display(function ($hq) use ($session_hq) {
            if ($hq) {
                return $hq . " <i class='fa fa-check text-success'></i>";
            }

            $hq = $this->composeProducts->sum(function ($item) {
                return ($item->product->length * $item->product->width * $item->product->height * $item->quantity) / 1000000;
            });

            return round($session_hq / $hq, 0);
        });

        $grid->column('ddp', 'DDP')->display(function () {
            return $this->composeProducts->sum(function ($item) {
                return $item->product->ddp * $item->quantity;
            });
        });

        $grid->column('order', __('序号'))->editable();

        $grid->disableExport();

        $grid->tools(function ($tools) use ($session_hq) {

            $tools->append('<a class="btn btn-sm btn-success pull-right p_update_control" data-toggle="modal" data-target="#change_hq"><i class="fa fa-chain"></i> <span class="hidden-xs">' . $session_hq . ' HQ</span></a>');

            $unit_text = Session::get('unit', 'cm') == 'cm' ? '厘米' : '英寸';

            $tools->append('<a style="margin-right: 5px" class="btn btn-sm btn-info pull-right p_update_control" data-toggle="modal" data-target="#change_unit"><i class="fa fa-codepen"></i> <span class="hidden-xs">' . $unit_text . '</span></a>');
        });

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
        $compose = Compose::with('composeProducts.product')->findOrFail($id);

        $compose->compose_products = $compose->composeProducts->map(function ($item) {
            $item['ddp'] = $item->product->ddp * $item->quantity;
            $item['volume'] = ($item->product->length * $item->product->width * $item->product->height * $item->quantity) / 1000000;

            return $item;
        });

        $session_hq = Session::get('hq', config('hq'));

        return view('compose', compact('compose', 'session_hq'));
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
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

    public function create(Content $content)
    {
        $this->loadVue();

        return $content->header('组合管理')
            ->description('创建')
            ->body('<compose></compose>');
    }

    public function store()
    {
        request()->validate([
            'name' => 'required|unique:composes,name',
            'asin' => 'required|unique:composes,asin',
            'hq'   => 'nullable',
        ], [
            'name.required' => '请输入组合名称',
            'name.unique'   => '该名称已存在',
            'asin.required' => '请输入 ASKU',
            'asin.unique'   => '该 ASKU 已存在',
        ]);

        $file = request()->file('images');
        $productInfo = request()->input('product_info');
        $productInfo = collect($productInfo)->where('deleted', 'false')->where('product_id', '!=', null);

        if ($productInfo->where('quantity', '<', 1)->count()) {
            return $this->setStatusCode(422)->responseError('单品数量必须大于0');
        }

        if ($productInfo->count() < 1) {
            return $this->setStatusCode(422)->responseError('必须添加单品');
        }

        if ($file) {
            $file = $this->saveAgreementFile($file);
            $file = $file->pluck('path')->toArray();
            $file = json_encode($file);
        }

        $now = Carbon::now();
        DB::beginTransaction(); //开启事务
        try {
            Storage::makeDirectory('public/composes/' . request()->input('asin'));
            $composeId = Compose::insertGetId([
                'name'       => request()->input('name'),
                'asin'       => request()->input('asin'),
                'hq'         => request()->input('hq'),
                'image'      => $file,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $productInfo = $productInfo->groupBy('product_id')->map(function ($item, $index) use ($composeId, $now) {
                return [
                    'compose_id' => $composeId,
                    'product_id' => $index,
                    'quantity'   => $item->sum('quantity'),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            });

            ComposeProduct::insert($productInfo->toArray());
            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }

    }

    public function compose()
    {
        request()->validate([
            'id'       => 'required',
            'name'     => 'required|unique:composes,name',
            'asin'     => 'required|unique:composes,asin',
            'quantity' => 'required|integer',
        ], [
            'name.required'     => '请输入组合名称',
            'name.unique'       => '组合名称已存在',
            'id.required'       => '缺少参数',
            'asin.required'     => '请输入 ASKU',
            'asin.unique'       => '该 ASKU 已存在',
            'quantity.required' => '请输入数量',
            'quantity.integer'  => '数量必须为整数',
        ]);

        $product = Product::findOrFail(request()->input('id'));

        $now = Carbon::now();
        DB::beginTransaction(); //开启事务

        try {
            Storage::makeDirectory('public/' . request()->input('asin'));
            $composeId = Compose::insertGetId([
                'name'       => request()->input('name'),
                'asin'       => request()->input('asin'),
                'hq'         => $product->hq,
                'image'      => $product->image ? json_encode([$product->image]) : null,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            ComposeProduct::create([
                'compose_id' => $composeId,
                'product_id' => $product->id,
                'quantity'   => request()->input('quantity'),
                'created_at' => $now,
                'updated_at' => $now
            ]);
            DB::commit();   //保存

            return response()->json(true);
        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }
    }

    protected function saveAgreementFile($file)
    {
        $file = FileService::saveFile($file, 'images');

        if (!$file || $file->where('status', 'FAIL')->count()) {

            throw new Exception('图片保存失败');
        }

        return $file;
    }

    public function composeSelect()
    {
        $q = request()->input('q');
        $products = Compose::where('name', 'like', '%'.$q.'%')->select('id', 'name as text')->get();

        return response()->json($products);
    }
}
