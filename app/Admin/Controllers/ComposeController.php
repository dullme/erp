<?php

namespace App\Admin\Controllers;

use DB;
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

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Compose);
        $grid->column('id', __('ID'));
        $grid->column('image', '图片')->display(function ($image) {
            return $image;
        })->image('', 100, 100);
        $grid->column('name', __('组合名称'));
        $grid->column('asin', __('ASIN'))->display(function () {
            return "<a href='/admin/media?path=/{$this->asin}'>{$this->asin}</a>";
        });
        $grid->column('box', '箱数')->display(function (){
            return $this->composeProducts->sum('quantity');
        });

        $grid->column('hq', '40HQ')->display(function () {
            $hq = $this->composeProducts->sum(function ($item){
                return ($item->product->length * $item->product->width * $item->product->height * $item->quantity) / 1000000;
            });
            return 65/$hq;
        });

        $grid->column('ddp', 'DDP')->display(function () {
            return $this->composeProducts->sum(function ($item){
                return $item->product->ddp * $item->quantity;
            });
        });

        $grid->column('created_at', __('添加时间'));

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
        $compose = Compose::with('composeProducts.product')->findOrFail($id);

        $compose->compose_products = $compose->composeProducts->map(function ($item) {
            $item['ddp'] = $item->product->ddp * $item->quantity;
            $item['volume'] = ($item->product->length * $item->product->width * $item->product->height * $item->quantity) / 1000000;

            return $item;
        });

        return view('compose', compact('compose'));
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
        $form->multipleImage('image', __('图片'))->removable();

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
        ], [
            'name.required' => '请输入组合名称',
            'name.unique'   => '该名称已存在',
            'asin.required' => '请输入 ASIN',
            'asin.unique'   => '该 ASIN 已存在',
        ]);

        $file = request()->file('images');
        $projectInfo = request()->input('project_info');
        $projectInfo = collect($projectInfo)->where('deleted', 'false')->where('project_id', '!=', null);

        if ($projectInfo->where('quantity', '<', 1)->count()) {
            return $this->setStatusCode(422)->responseError('单品数量必须大于0');
        }

        if ($projectInfo->count() < 1) {
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
            $composeId = Compose::insertGetId([
                'name'       => request()->input('name'),
                'asin'       => request()->input('asin'),
                'image'      => $file,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $projectInfo = $projectInfo->groupBy('project_id')->map(function ($item, $index) use ($composeId, $now) {
                return [
                    'compose_id' => $composeId,
                    'product_id' => $index,
                    'quantity'   => $item->sum('quantity'),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            });

            ComposeProduct::insert($projectInfo->toArray());
            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }

    }

    protected function saveAgreementFile($file)
    {
        $file = FileService::saveFile($file, 'compose');

        if (!$file || $file->where('status', 'FAIL')->count()) {

            throw new Exception('图片保存失败');
        }

        return $file;
    }
}
