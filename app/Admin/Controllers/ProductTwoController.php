<?php

namespace App\Admin\Controllers;

use App\Order;
use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Session;

class ProductTwoController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '单品管理';

    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid())
            ->body(view('change_hq'))
            ->body(view('add_product'));
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Product);

        $session_hq = Session::get('hq', config('hq'));
        $unit_text = Session::get('unit', 'cm') == 'in' ? 'cu ft' : 'm³';
        $session_unit = Session::get('unit', 'cm');
        $session_weight = Session::get('weight', 'kg');

//        $grid->column('id', __('ID'));
        $grid->column('sku', __('SKU'));
        $grid->column('image', __('图片'))->image();
        $grid->column('description', __('描述'));
        $grid->column('length', __('长（' . $session_unit . '）'))->display(function ($length) use ($session_unit) {
            return $session_unit == 'in' ? round($length * config('in'), 2) : $length;
        });
        $grid->column('width', __('宽（' . $session_unit . '）'))->display(function ($width) use ($session_unit) {
            return $session_unit == 'in' ? round($width * config('in'), 2) : $width;
        });
        $grid->column('height', __('高（' . $session_unit . '）'))->display(function ($height) use ($session_unit) {
            if ($session_unit == 'in') {
                return round($height * config('in'), 2) . ' in';
            }

            return $session_unit == 'in' ? round($height * config('in'), 2) : $height;
        });
        $grid->column('volume', '体积（'.$unit_text.'）')->display(function () use ($session_unit) {
            $volume = $this->length * $this->width * $this->height / 1000000;
            if ($session_unit == 'in') {
                $volume = $volume * config('cuft');
                return round($volume, 2);
            }

            return round($volume, 2);
        });
        $grid->column('weight', __('毛重（'.$session_weight.'）'))->display(function ($weight) use($session_weight){
            return $session_weight == 'kg' ? $weight : round(config('lb') * $weight, 2);
        });
        $grid->column('coefficient', '系数')->display(function () {
            return ($this->width + $this->height) * 2 + $this->length;
        });

        $grid->column('hq', '箱规'.getHq($session_hq))->display(function ($hq) use ($session_hq) {
            if ($hq) {
                return $hq . " <i class='fa fa-check text-success'></i>";
            }

            if($this->length != 0 && $this->width != 0 && $this->height != 0){
                $hq = $session_hq / ($this->length * $this->width * $this->height / 1000000);
            }else{
                $hq = 0;
            }

            return round($hq, 0);
        });

        $grid->column('ddp', __('DDP'));

        $grid->column('in', '生成组合')->display(function (){
            return "<a style='cursor: pointer' data-toggle='modal' data-target='#add_product' title='生成组合' onclick=\"addProduct({$this->id},'{$this->description}', '{$this->sku}')\"><i class='fa fa-cubes'></i></a>";
        });

//        $grid->column('created_at', __('添加时间'));

        $grid->disableExport();
        $grid->disableRowSelector();


        $grid->tools(function ($tools) use ($session_hq) {

            $tools->append('<a class="btn btn-sm btn-success pull-right p_update_control" data-toggle="modal" data-target="#change_hq"><i class="fa fa-chain"></i> <span class="hidden-xs">' . getHq($session_hq) . '</span></a>');

            $unit_text = Session::get('unit', 'cm') == 'cm' ? '厘米' : '英寸';

            $tools->append('<a style="margin-right: 5px" class="btn btn-sm btn-info pull-right p_update_control" data-toggle="modal" data-target="#change_unit"><i class="fa fa-codepen"></i> <span class="hidden-xs">' . $unit_text . '</span></a>');

            $weight_text = Session::get('weight', 'kg') == 'kg' ? '千克' : '磅';

            $tools->append('<a style="margin-right: 5px" class="btn btn-sm btn-warning pull-right p_update_control" data-toggle="modal" data-target="#change_weight"><i class="fa fa-balance-scale"></i> <span class="hidden-xs">' . $weight_text . '</span></a>');
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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('sku', __('SKU'));
        $show->field('length', __('长（厘米）'));
        $show->field('width', __('宽（厘米）'));
        $show->field('height', __('高（厘米）'));
        $show->field('weight', __('毛重（公斤）'));
        $show->field('ddp', __('DDP'));
        $show->field('image', __('图片'))->image();
        $show->field('description', __('描述'));
        $show->field('created_at', __('添加时间'));
        $show->field('content', __('详情'))->unescape();

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Product);

        $form->text('sku', __('SKU'))
            ->creationRules(['required', "unique:products"])
            ->updateRules(['required', "unique:products,sku,{{id}}"]);
        $form->decimal('length', __('长（厘米）'))->required();
        $form->decimal('width', __('宽（厘米）'))->required();
        $form->decimal('height', __('高（厘米）'))->required();
        $form->decimal('weight', __('毛重（公斤）'))->required();
        $form->number('ddp', __('DDP'))->default(0);
        $form->text('hq', __('HQ'));
        $form->select('unit', __('单位'))->options(getUnit());
        $form->image('image', __('图片'));
        $form->text('description', __('描述'));
        $form->UEditor('content', __('详情'));

        return $form;
    }

    public function product()
    {
        $q = request()->input('q');
        $order_id = request()->input('order_id');
        $products = Product::where('sku', 'like', '%' . $q . '%')
            ->orWhere('description', 'like', '%' . $q . '%')
            ->select('id', 'sku as text', 'description', 'ddp', 'image')->get();

        if($order_id){
            $order = Order::with('orderProduct', 'warehouses')->find($order_id);
            $products->map(function ($item)use($order){
                $item['needed'] = $order->orderProduct->where('product_id',$item['id'])->sum('quantity');
                $item['has'] = $order->warehouses->where('product_id',$item['id'])->sum('quantity');
                $item['need'] = $item['needed'] - $item['has'];

                return $item;
            });
        }

        return response()->json($products);
    }
}
