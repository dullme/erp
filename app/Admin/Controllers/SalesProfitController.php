<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\SalesProfitImport;
use App\Imports\SalesProfitsImport;
use App\SalesProfit;
use App\WarehouseCompany;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Grid;
use Excel;
use Illuminate\Http\Request;
use function foo\func;

class SalesProfitController extends ResponseController
{

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '销售利润表';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new SalesProfit);
        $grid->model()->orderByDesc('created_at');
        $grid->tools(function ($tools) {
            $tools->append(new SalesProfitImport());
        });

//        $grid->column('id', __('Id'));
        $grid->column('asin', __('ASKU'));
        $grid->column('quantity', __('数量'));
        $grid->column('ddp', __('DDP'))->display(function ($ddp){
            return '$ '.$ddp;
        });
        $grid->column('payment','金额')->display(function (){
            return '$ '.bigNumber($this->quantity * $this->ddp)->getValue();
        });
        $grid->column('created_at', __('转入时间'))->display(function ($date){
            return substr($date, 0, 10);
        });
        $grid->column('updated_at', __('导入时间'));

        $grid->disableFilter();
        $grid->disableActions();
        $grid->disableColumnSelector();
        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableRowSelector();

        return $grid;
    }

    public function import(Request $request)
    {
        $now = Carbon::now()->toDateTimeString();
        $importData = Excel::toCollection(new SalesProfitsImport, $request->file('file'))[0]; //Excel 导入的数据

        $importData = $importData->forget([0, 1])->map(function ($item) use ($now) {
            return [
                'asin'         => (string) $item[0],
                'quantity'     => $item[1],
                'ddp'          => $item[3],
                'created_at'   => date('Y-m-d', ($item[2] - 25569) * 24 * 3600),
                'updated_at'   => $now,
            ];
        })->where('asin', '!=', '');

        if ($importData->count() == 0) {
            return $this->responseError('Excel 中没有数据');
        }

        $lastSalesProfit = SalesProfit::orderBy('created_at', 'DESC')->first();

        if($lastSalesProfit){
            $importData = $importData->where('created_at', '>', $lastSalesProfit->created_at);
        }

        if($importData->count() == 0){
            return $this->responseError('没有新增的数据');
        }

        SalesProfit::insert($importData->values()->toArray());

        return $this->responseSuccess(true);
    }
}
