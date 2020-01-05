<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">详情</h3>

        <div class="box-tools">
            <div class="pull-right grid-create-btn" style="margin-right: 10px">
                <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-default" title="列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 列表</span>
                </a>
            </div>

            <div class="pull-right grid-create-btn" style="margin-right: 10px">
                <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-default">
                    <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;验货入库</span>
                </button>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row" style="margin-bottom: 20px">
            <div class="col-lg-9">
                <p>订单编号：{{ $order->no }}</p>
                <p>供应商：{{ $order->supplier->name }}</p>
                <p>供应商电话：{{ $order->supplier->mobile }}</p>
                <p>签订日：{{ $order->signing_at }}</p>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">订单</div>
                <table class="table table-bordered text-center">
                    <thead>
                    <tr>
                        <th>图片</th>
                        <th>SKU</th>
                        <th>数量</th>
                        <th>单价</th>
                        <th>合计</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order['product'] as $item)
                        <tr>
                            <td>
                                @if($item['image'])
                                    <img width="100" src="{{ asset('uploads/'.$item['image']) }}">
                                @endif
                            </td>
                            <td>{{ $item['sku'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['price'] }}</td>
                            <td>{{ $item['total'] }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="4"></td>
                        <td> {{ bigNumber(collect($order['product'])->sum('total'))->getValue() }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        @if($order->product_batch)
            <div class="col-lg-8 col-md-12">
                @foreach($order->product_batch as $key=>$product)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span>第 {{ $key }} 次入库记录</span>
                            <span style="float: right">入库时间：{{ substr($product[0]['entry_at'],0, 10) }}</span>
                        </div>
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>图片</th>
                                <th>SKU</th>
                                <th>数量</th>
                                <th>单价</th>
                                <th>合计</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product as $item)
                                <tr>
                                    <td>
                                        @if($item['image'])
                                            <img width="100" src="{{ asset('uploads/'.$item['image']) }}">
                                        @endif
                                    </td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
                                    <td>{{ $item['price'] }}</td>
                                    <td>{{ $item['total'] }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4"></td>
                                <td> {{ bigNumber(collect($product)->sum('total'))->getValue() }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif


    </div>
</div>


<div class="modal fade in" id="modal-default">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span></button>
                <h4 class="modal-title">验货入库</h4>
            </div>
            <div class="modal-body">
                <warehouse no="{{ $order->no }}" order_id="{{ $order->id }}"
                           product="{{json_encode($order['product'])}}"></warehouse>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
