<?php

namespace App\Admin\Controllers;

use Session;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;

class HomeController extends Controller
{

    public function index(Content $content)
    {
        return $content
            ->title('Dashboard')
            ->description('Description...')
            ->row(Dashboard::title())
            ->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
    }

    public function changeHq()
    {
        $hq = request()->input('hq');


        Session::put('hq', $hq);

        return response()->json(Session::all());
    }

    public function changeUnit()
    {
        $unit = request()->input('unit');


        Session::put('unit', $unit);

        return response()->json(Session::all());
    }

    public function changeWeight()
    {
        $weight = request()->input('weight');


        Session::put('weight', $weight);

        return response()->json(Session::all());
    }
}
