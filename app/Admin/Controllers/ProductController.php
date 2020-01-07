<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ButtonUpdateControl;
use App\Product;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Session;

class ProductController extends AdminController
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
        $unit_text = Session::get('unit', 'cm') == 'in' ? '英寸' : '厘米';
        $unit_text2 = Session::get('unit', 'cm') == 'in' ? '立方英尺' : '立方米';
        $session_unit = Session::get('unit', 'cm');

//        $grid->column('id', __('ID'));
        $grid->column('sku', __('SKU'));
        $grid->column('image', __('图片'))->image();
        $grid->column('description', __('描述'));
        $grid->column('length', __('长（' . $unit_text . '）'))->display(function ($length) use ($session_unit) {
            if ($session_unit == 'in') {
                return round($length * config('in'), 2) . ' in';
            }

            return $length . ' cm';
        });
        $grid->column('width', __('宽（' . $unit_text . '）'))->display(function ($width) use ($session_unit) {
            if ($session_unit == 'in') {
                return round($width * config('in'), 2) . ' in';
            }

            return $width . ' cm';
        });
        $grid->column('height', __('高（' . $unit_text . '）'))->display(function ($height) use ($session_unit) {
            if ($session_unit == 'in') {
                return round($height * config('in'), 2) . ' in';
            }

            return $height . ' cm';
        });
        $grid->column('volume', '体积（'.$unit_text2.'）')->display(function () use ($session_unit) {
            $volume = $this->length * $this->width * $this->height / 1000000;
            if ($session_unit == 'in') {
                $volume = $volume * config('cuft');
                return round($volume, 2) . ' cu ft';
            }

            return round($volume, 2) . ' m³';
        });
        $grid->column('weight', __('毛重（公斤）'));
        $grid->column('coefficient', '系数')->display(function () {
            return ($this->width + $this->height) * 2 + $this->length . ' kg';
        });

        $grid->column('hq', $session_hq . ' HQ')->display(function ($hq) use ($session_hq) {
            if ($hq) {
                return $hq . " <i class='fa fa-check text-success'></i>";
            }

            $hq = $session_hq / ($this->length * $this->width * $this->height / 1000000);

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
        $show = new Show(Product::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('sku', __('SKU'));
        $show->field('length', __('长（厘米）'));
        $show->field('width', __('宽（厘米）'));
        $show->field('height', __('高（厘米）'));
        $show->field('weight', __('毛重（公斤）'));
        $show->field('ddp', __('DDP'));
        $show->field('image', __('图片'));
        $show->field('description', __('描述'));
        $show->field('created_at', __('添加时间'));
        $show->field('content', __('详情'));

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

        $form->text('sku', __('SKU'));
        $form->number('length', __('长（厘米）'));
        $form->number('width', __('宽（厘米）'));
        $form->number('height', __('高（厘米）'));
        $form->decimal('weight', __('毛重（公斤）'));
        $form->number('ddp', __('DDP'));
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
        $products = Product::where('sku', 'like', '%' . $q . '%')
            ->orWhere('description', 'like', '%' . $q . '%')
            ->select('id', 'sku as text', 'description', 'ddp', 'image')->get();

        return response()->json($products);
    }
}
