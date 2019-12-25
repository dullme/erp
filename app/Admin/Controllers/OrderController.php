<?php

namespace App\Admin\Controllers;

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
    protected $title = '订单管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Order);

        $grid->column('id', __('ID'));
        $grid->column('no', __('订单编号'));
        $grid->column('batch', __('生产批号'));
        $grid->column('remark', __('备注'));
        $grid->column('created_at', __('添加时间'));

//        $grid->column('updated_at', __('Updated at'));

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
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('no', __('订单编号'));
        $show->field('batch', __('生产批号'));
        $show->field('remark', __('备注'));
        $show->field('created_at', __('添加时间'));

//        $show->field('updated_at', __('Updated at'));

        return $show;
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
            'no'    => 'required|unique:orders,no',
            'batch' => 'required|unique:orders,batch',
        ], [
            'no.required'    => '请输入订单编号',
            'no.unique'      => '该订单编号已存在',
            'batch.required' => '请输入生产批号',
            'batch.unique'   => '该生产批号已存在',
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
            $orderId = Order::insertGetId([
                'no'         => request()->input('no'),
                'batch'      => request()->input('batch'),
//                'image'      => $file,
                'created_at' => $now,
                'updated_at' => $now
            ]);

            $projectInfo = $projectInfo->groupBy('project_id')->map(function ($item, $index) use ($orderId, $now) {
                return [
                    'order_id'   => $orderId,
                    'product_id' => $index,
                    'status'     => 1, //中国仓库
                    'quantity'   => $item->sum('quantity'),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            });

            Warehouse::insert($projectInfo->toArray());
            DB::commit();   //保存

            return $this->responseSuccess('添加成功');

        } catch (\Exception $exception) {
            DB::rollBack(); //回滚

            return $this->setStatusCode(422)->responseError($exception->getMessage());
        }

    }
}
