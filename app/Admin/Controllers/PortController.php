<?php

namespace App\Admin\Controllers;

use App\Port;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class PortController extends ResponseController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '港口管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Port);

        $grid->column('name', __('港口名称'));
        $grid->column('type', __('国内/海外'))->display(function ($type){
            return $type == 0 ?'国内':'海外';
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
        $show = new Show(Port::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('港口名称'));
        $show->field('type', __('国内/海外'))->using([0 => '国内', 1 => '海外']);

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Port);

        $form->text('name', __('港口名称'));
        $form->switch('type', __('国内/海外'))->states([
            'on'  => ['value' => 1, 'text' => '海外'],
            'off' => ['value' => 0, 'text' => '国内'],
        ]);

        return $form;
    }

    public function getPort()
    {
        $port = Port::select('id', 'name')->orderBy('type', 'ASC')->get();
        $port2 = Port::select('id', 'name')->orderBy('type', 'DESC')->get();

        $ship_port = $port->toArray();
        $arrival_port = $port2->toArray();

        return $this->responseSuccess([
            'ship_port' => $ship_port,
            'arrival_port' => $arrival_port,
        ]);

    }

    public function getPortSelect1()
    {
        $port = Port::select('id', 'name as text')->orderBy('type', 'ASC')->get();

        return $port->map(function ($item){
            return [
                'id' => $item['text'],
                'text' => $item['text'],
            ];
        });
    }

    public function getPortSelect2()
    {
        $port = Port::select('id', 'name as text')->orderBy('type', 'DESC')->get();

        return $port->map(function ($item){
            return [
                'id' => $item['text'],
                'text' => $item['text'],
            ];
        });
    }
}
