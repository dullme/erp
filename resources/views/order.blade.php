<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">详情</h3>

        <div class="box-tools">
            <div class="pull-right grid-create-btn" style="margin-right: 10px">
                <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-default" title="列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 列表</span>
                </a>
            </div>

            @if($order->status == 0)
                @if($order->orderBatch->count())
                    <div class="pull-right grid-create-btn" style="margin-right: 10px">
                        <button type="button" class="btn btn-sm btn-info" data-id="{{ $order->id }}" id="order-finish">
                            <i class="fa fa-check"></i><span class="hidden-xs">&nbsp;&nbsp;完结订单</span>
                        </button>
                    </div>
                    @else
                    <div class="pull-right grid-create-btn" style="margin-right: 10px">
                        <button type="button" class="btn btn-sm btn-danger" data-id="{{ $order->id }}" id="order-delete">
                            <i class="fa fa-times"></i><span class="hidden-xs">&nbsp;&nbsp;删除订单</span>
                        </button>
                    </div>
                @endif

                <div class="pull-right grid-create-btn" style="margin-right: 10px">
                    <button type="button" class="btn btn-sm btn-default" data-toggle="modal" data-target="#modal-default">
                        <i class="fa fa-plus"></i><span class="hidden-xs">&nbsp;&nbsp;验货入库</span>
                    </button>
                </div>
            @else
                <div class="pull-right grid-create-btn" style="margin-right: 10px;margin-top: 5px">
                    <i class="fa fa-check text-success"> 订单于 {{ $order->finished_at }} 完结</i>
                </div>
            @endif


        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row" style="margin-bottom: 20px">
            <div class="col-lg-9">
                <p>订单编号：{{ $order->no }}</p>
                <p>生产商：{{ optional($order->supplier)->name }}</p>
                <p>生产商电话：{{ optional($order->supplier)->mobile }}</p>
                <p>进口商：{{ optional($order->customer)->name }}</p>
                <p>进口商电话：{{ optional($order->customer)->mobile }}</p>
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
                        <th>验货时间</th>
                        <th>备注</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order['orderProduct'] as $item)
                        <tr>
                            <td>
                                @if($item['product']['image'])
                                    <img width="100" src="{{ asset('uploads/'.$item['product']['image']) }}">
                                @endif
                            </td>
                            <td>{{ $item['product']['sku'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ $item['price'] }}</td>
                            <td>{{ round($item['quantity'] * $item['price'], 2) }}</td>
                            <td>{{ substr($item['inspection_at'],0,10) }}</td>
                            <td>{{ $item['remark'] }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($order->orderBatch)
            <div class="col-lg-8 col-md-12">
                @foreach($order->orderBatch as $product)
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span>第 {{ $product['batch'] }} 次入库记录</span>
                            <span style="margin-left: 20px">入库时间：{{ substr($product['entry_at'],0, 10) }}</span>
                            @if(!$product['status'])
                                <span data-id="{{ $product['id'] }}" style="float: right;margin-top: -5px;margin-left: 10px" class="btn btn-success btn-sm preview">审核</span>
                                <span data-id="{{ $product['id'] }}" style="float: right;margin-top: -5px;margin-left: 10px" class="btn btn-danger btn-sm preview-delete">删除</span>
                            @else
                                <i style="float: right;font-size: 20px"  class="fa fa-check text-success"></i>
                            @endif

                        </div>
                        <table class="table table-bordered text-center">
                            <thead>
                            <tr>
                                <th>图片</th>
                                <th>SKU</th>
                                <th>数量</th>
{{--                                <th>单价</th>--}}
{{--                                <th>合计</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($product->product_batch as $item)
                                <tr>
                                    <td>
                                        @if($item['image'])
                                            <img width="100" src="{{ asset('uploads/'.$item['image']) }}">
                                        @endif
                                    </td>
                                    <td>{{ $item['sku'] }}</td>
                                    <td>{{ $item['quantity'] }}</td>
{{--                                    <td>{{ $item['price'] }}</td>--}}
{{--                                    <td>{{ $item['total'] }}</td>--}}
                                </tr>
                            @endforeach
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
                <warehouse no="{{ $order->no }}" order_id="{{ $order->id }}"></warehouse>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
