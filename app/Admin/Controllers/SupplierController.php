<?php

namespace App\Admin\Controllers;

use App\Admin\Actions\Post\Restore;
use App\Admin\Extensions\SupplierImport;
use App\Buyer;
use Excel;
use App\Imports\SuppliersImport;
use App\Supplier;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class SupplierController extends ResponseController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '生产商';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Supplier);
        $grid->model()->orderByDesc('id');
        $grid->tools(function ($tools){
            $tools->append(new SupplierImport());
        });
        $grid->disableExport();
        $grid->disableRowSelector();

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', '名称');
            $filter->like('mobile', '电话');

            // 范围过滤器，调用模型的`onlyTrashed`方法，查询出被软删除的数据。
            $filter->scope('trashed', '回收站')->onlyTrashed();
        });

        $grid->actions(function ($actions) {

            if (\request('_scope_') == 'trashed') {
                $actions->add(new Restore());
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->disableView();
            }

        });

        $grid->column('name', __('名称'));
        $grid->column('english_name', __('英文名称'));
        $grid->column('contact_person', __('联系人'));
        $grid->column('position', __('职位'));
        $grid->column('mobile', __('手机号码'));
        $grid->column('tel', __('办公室电话'));
        $grid->column('fax', __('传真号码'));
        $grid->column('email', __('邮箱'));
        $grid->column('website', __('网站'));
        $grid->column('address', __('地址'))->display(function ($address){
            $short = mb_substr($address, 0, 10);
            return "<span data-toggle='tooltip' data-placement='top\' title='' data-original-title='{$address}'>{$short}</span>";
        });
        $grid->column('supply', __('境内货源地'));
        $grid->column('tax_id', __('税号'));
        $grid->column('bank', __('开户行'));
        $grid->column('bank_account', __('账号'));
        $grid->column('remark', __('备注'));
        $grid->column('register', __('登记'));
        $grid->column('created_at', __('添加时间'));

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
        $show = new Show(Supplier::findOrFail($id));

        $show->field('name', __('名称'));
        $show->field('english_name', __('英文名称'));
        $show->field('contact_person', __('联系人'));
        $show->field('position', __('职位'));
        $show->field('mobile', __('手机号码'));
        $show->field('tel', __('办公室电话'));
        $show->field('fax', __('传真号码'));
        $show->field('email', __('邮箱'));
        $show->field('website', __('网站'));
        $show->field('address', __('地址'));
        $show->field('supply', __('境内货源地'));
        $show->field('tax_id', __('税号'));
        $show->field('bank', __('开户行'));
        $show->field('bank_account', __('账号'));
        $show->field('register', __('备注'));
        $show->field('created_at', __('添加时间'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Supplier);

        $form->text('name', __('名称'));
        $form->text('english_name', __('英文名称'));
        $form->text('contact_person', __('联系人'));
        $form->text('position', __('职位'));
        $form->mobile('mobile', __('手机号码'));
        $form->text('tel', __('办公室电话'));
        $form->text('fax', __('传真号码'));
        $form->email('email', __('邮箱'));
        $form->text('website', __('网站'));
        $form->text('address', __('地址'));
        $form->text('supply', __('境内货源地'));
        $form->text('tax_id', __('税号'));
        $form->text('bank', __('开户行'));
        $form->text('bank_account', __('账号'));
        $form->text('register', __('登记'));
        $form->textarea('remark', __('备注'));

        return $form;
    }

    public function supplier()
    {
        $q = request()->input('q');
        $products = Supplier::where('name', 'like', '%'.$q.'%')->get();

        return response()->json($products);
    }

    public function import(Request $request)
    {
        $now = Carbon::now()->toDateString();
        $importData = Excel::toCollection(new SuppliersImport, $request->file('file'))[0]; //Excel 导入的数据
        $importData = $importData->forget(0)->map(function ($item) use ($now) {
            return [
                'name'           => (string) $item[0],
                'english_name'   => (string) isset($item[1]) ? $item[1] : null,
                'contact_person' => (string) isset($item[2]) ? $item[2] : null,
                'position'       => (string) isset($item[3]) ? $item[3] : null,
                'mobile'         => (string) isset($item[4]) ? $item[4] : null,
                'tel'            => (string) isset($item[5]) ? $item[5] : null,
                'fax'            => (string) isset($item[6]) ? $item[6] : null,
                'email'          => (string) isset($item[7]) ? $item[7] : null,
                'website'        => (string) isset($item[8]) ? $item[8] : null,
                'address'        => (string) isset($item[9]) ? $item[9] : null,
                'supply'         => (string) isset($item[10]) ? $item[10] : null,
                'tax_id'         => (string) isset($item[11]) ? $item[11] : null,
                'bank'           => (string) isset($item[12]) ? $item[12] : null,
                'bank_account'   => (string) isset($item[13]) ? $item[13] : null,
                'register'       => (string) isset($item[14]) ? $item[14] : null,
                'remark'         => (string) isset($item[15]) ? $item[15] : null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        })->where('name', '!=', '');

        if($importData->count() == 0){
            return $this->responseError('Excel 中没有数据');
        }

        $suppliers = Supplier::whereIn('name', $importData->pluck('name')->toArray())->withTrashed()->get();

        if ($suppliers->count()) {
            return $this->responseError(implode(',', $suppliers->pluck('name')->toArray()).'数据库中已存在');
        }

        $res = $importData->groupBy('name')->map(function ($item){
            return [
                'count' => $item->count()
            ];
        })->where('count', '>', 1);

        if($res->keys()->count() > 0){
            return $this->responseError(implode(',', $res->keys()->toArray()).'有重复项');
        }

        Supplier::insert($importData->values()->toArray());

        return $this->responseSuccess(true);
    }
}
