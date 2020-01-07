<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">详情</h3>

        <div class="box-tools">
            <div class="pull-right grid-create-btn" style="margin-right: 10px">
                <a href="{{ url('/admin/packages') }}" class="btn btn-sm btn-default" title="列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 列表</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row" style="margin-bottom: 20px">
            <div class="col-lg-9">
                <p>货代：{{ $package->forwarding_company_id }}</p>
                <p>提单号：{{ $package->lading_number }}</p>
                <p>集装箱号：{{ $package->container_number }}</p>
                <p>铅封号：{{ $package->seal_number }}</p>
            </div>
        </div>

        <div class="col-lg-8 col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading">箱内物品</div>
                <table class="table table-bordered text-center">
                    <thead>
                    <tr>
                        <th>图片</th>
                        <th>编号 <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="订单编号-SKU-入库批次-入库周期"></i></th>
                        <th>SKU</th>
                        <th>数量</th>
                        <th>状态</th>
                        <th>仓储</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($package->warehouse as $item)
                        <tr>
                            <td>
                                @if($item['image'])
                                    <img width="100" src="{{ asset('uploads/'.$item['image']) }}">
                                @endif
                            </td>
                            <td>
                                @foreach($item['batch_number'] as $key=>$bn)
                                    <p><a href="{{ url('/admin/orders/'.$bn->order_id) }}">{{ $bn->batch_number }}:{{ $bn->quantity }}</a></p>
                                @endforeach
                            </td>
                            <td>{{ $item['sku'] }}</td>
                            <td>{{ $item['quantity'] }}</td>
                            <td>{{ getStatusText($item['status']) }}</td>
                            <td>{{ $item['warehouse_company'] ?? '等待入仓' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
