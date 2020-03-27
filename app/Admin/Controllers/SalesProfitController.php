<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\SalesProfitImport;
use App\Compose;
use App\Imports\SalesProfitsImport;
use App\SalesProfit;
use App\WarehouseCompany;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Excel;
use Illuminate\Http\Request;
use function foo\func;

class SalesProfitController extends ResponseController
{

    use AdminControllerTrait;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '销售利润表';

    public function index(Content $content)
    {
        $warehouse_company = WarehouseCompany::orderBy('id', 'DESC')->pluck('name', 'id');

        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid())
            ->body(view('upload_sales', compact('warehouse_company')));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SalesProfit);
        $grid->model()->orderByDesc('created_at');
//        $grid->tools(function ($tools) {
//            $tools->append(new SalesProfitImport());
//        });

//        $grid->column('id', __('Id'));
        $grid->column('asin', __('ASKU'));
        $grid->column('quantity', __('数量'));
        $grid->column('ddp', __('DDP'))->display(function ($ddp) {
            return '$ ' . $ddp;
        });
        $grid->column('payment', '金额')->display(function () {
            return '$ ' . bigNumber($this->quantity * $this->ddp)->getValue();
        });
        $grid->column('created_at', __('销售时间'))->display(function ($date) {
            return substr($date, 0, 10);
        });
        $grid->column('updated_at', __('导入时间'));

        $grid->tools(function ($tools) {

            $tools->append('<a class="btn btn-sm btn-danger pull-right p_update_control" data-toggle="modal" data-target="#upload-sales"><i class="fa fa-upload"></i> <span class="hidden-xs">导入</span></a>');

        });

        $grid->filter(function ($filter) {
            $warehouse_company = WarehouseCompany::orderBy('id', 'DESC')->pluck('name', 'id');
            $filter->equal('warehouse_company_id', '仓储公司')->select($warehouse_company);
            $filter->disableIdFilter();
        });

//        $grid->disableFilter();
        $grid->disableActions();
        $grid->disableColumnSelector();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        return $grid;
    }

    public function import(Request $request)
    {
        $id = $request->input('id');

        if (!$id) {
            return $this->responseError('请选择仓储公司');
        }

        $now = Carbon::now()->toDateTimeString();
        try {
            $importData = Excel::toCollection(new SalesProfitsImport, $request->file('file'))[0]; //Excel 导入的数据

            $importData = $importData->forget([0, 1])->map(function ($item) use ($now, $id) {
                return [
                    'warehouse_company_id' => $id,
                    'asin'                 => (string) $item[0],
                    'quantity'             => $item[1],
                    'ddp'                  => $item[3],
                    'created_at'           => date('Y-m-d', ($item[2] - 25569) * 24 * 3600),
                    'updated_at'           => $now,
                ];
            })->where('asin', '!=', '');

        }catch (\Exception $exception){
            return $this->responseError('上传失败请检查 Excel 文件内容是否正确');
        }

        $asins = $importData->pluck('asin')->unique()->values()->toArray();
        $compose = Compose::with('onlyComposeProducts')->whereIn('asin', $asins)->get();
        $compose_asin = $compose->pluck('asin')->toArray();
        $diff_asins = array_diff($asins, $compose_asin);
        if(count($diff_asins)){
            return $this->responseError('系统中不存以下 ASKU <br/>'.implode('、', $diff_asins));
        }


        if ($importData->count() == 0) {
            return $this->responseError('Excel 中没有数据');
        }

        $lastSalesProfit = SalesProfit::where('warehouse_company_id', $id)->orderBy('created_at', 'DESC')->first();

        if ($lastSalesProfit) {
            $importData = $importData->where('created_at', '>', $lastSalesProfit->created_at);
        }

        if ($importData->count() == 0) {
            return $this->responseError('没有新增的数据');
        }

        $importData = $importData->map(function ($item) use ($compose){
            $compose_asin = $compose->where('asin', $item['asin'])->first()->onlyComposeProducts;
            $item['products'] = $compose_asin->map(function ($item){
                return [
                    'id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ];
            });

            return $item;
        });

        SalesProfit::insert($importData->values()->toArray());

        return $this->responseSuccess(true);
    }
}
