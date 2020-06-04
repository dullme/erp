<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">详情</h3>

        <div class="box-tools">
            <div class="pull-right grid-create-btn" style="margin-right: 10px">
                <a href="{{ url('/admin/packages') }}" class="btn btn-sm btn-default" title="列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 列表</span>
                </a>
            </div>

            @if($package->status == 0)
                <div class="pull-right grid-create-btn" style="margin-right: 10px">
                    <button type="button" class="btn btn-sm btn-success" data-id="{{ $package->id }}" id="preview-package">
                        <i class="fa fa-check"></i><span class="hidden-xs"> 审核</span>
                    </button>
                </div>
            @endif

            @if($package->status == 1)
                <div class="pull-right grid-create-btn" style="margin-right: 10px;margin-top: 5px">
                    <span class="label label-info" style="padding: 8px 10px;">待入库</span>
                </div>

                <div class="pull-right grid-create-btn" style="margin-right: 10px;margin-top: 5px">
                    <a href="{{ url('/admin/api/package/download/'.$package->id) }}" target="_blank" class="label btn-sm label-default" style="padding: 8px 10px;"><i class="fa fa-download"> 发货清单</i></a>
                </div>
            @endif

            @if($package->status == 2)
                <div class="pull-right grid-create-btn" style="margin-right: 10px;margin-top: 5px">
                    <span class="label btn-sm label-success" style="padding: 8px 10px;">已入库</span>
                </div>

                <div class="pull-right grid-create-btn" style="margin-right: 10px;margin-top: 5px">
                    <a href="{{ url('/admin/api/package/download/'.$package->id) }}" target="_blank" class="label btn-sm label-default" style="padding: 8px 10px;"><i class="fa fa-download"> 发货清单</i></a>
                </div>
            @endif

        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row" style="margin-bottom: 20px">
            <div class="col-lg-8">
                <div class="col-lg-6">
                    <p>货代：{{ $package->forwardingCompany->name }}</p>
                    <p>出口商：{{ $package->buyer->name }}</p>
                    <p>进口商：{{ $package->customer->name }}</p>
                    <p>提单号：{{ $package->lading_number }}</p>
                    <p>合同号：{{ $package->agreement_no }}</p>
                    <p>集装箱号：{{ $package->container_number }}</p>
                    <p>铅封号：{{ $package->seal_number }}</p>
                    <p>发货港：{{ $package->ship_port }}</p>
                </div>
                <div class="col-lg-6">
                    <p>到货港：{{ $package->arrival_port }}</p>
                    <p>发货日：{{ substr($package->packaged_at, 0, 10) }}</p>
                    <p>离港时间：{{ substr($package->departure_at, 0, 10) }}</p>
                    <p>到港时间：{{ substr($package->arrival_at, 0, 10) }}</p>
                    <p>预计入仓时间：{{ substr($package->entry_at, 0, 10) }}</p>
                    <p>仓储公司：{{ $package->warehouseCompany->name }}</p>
                    @if($package->status == 2)
                        <p>实际入仓时间：{{ $package->checkin_at }}</p>
                    @endif
                </div>
            </div>

            <div class="col-lg-12">
                <div class="col-lg-8">
                    <textarea rows="5" placeholder="备注" class="form-control remark">{{ $package->remark }}</textarea>
                </div>
            </div>
        </div>

        @if($package->status == 0)
            <div class="col-lg-8 col-md-12">
                <div class="panel panel-info">
                    <div class="panel-heading">箱内物品</div>
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>图片</th>
                            <th>SKU</th>
                            <th>数量</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($package->product as $item)
                            <tr>
                                <td>
                                    @if($item['image'])
                                        <img width="100" src="{{ asset('uploads/'.$item['image']) }}">
                                    @endif
                                </td>
                                <td>{{ $item['sku'] }}</td>
                                <td>{{ $item['quantity'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        @if(in_array($package->status, [1,2]))

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

        @endif

        @if($package->items->count())
            <div class="col-lg-8 col-md-12">
                <div class="panel panel-success">
                    <div class="panel-heading">赠品</div>
                    <table class="table table-bordered text-center">
                        <thead>
                        <tr>
                            <th>名称</th>
                            <th>数量</th>
                            <th>备注</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($package->items as $item)
                                <tr>
                                    <td>{{ $item->item->name }}</td>
                                    <td>{{ $item->quantity }}/{{ $item->item->unit }}</td>
                                    <td>{{ $item->item->remark }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    $(function () {
        $('#preview-package').on('click', function () {
            let id = $(this).attr('data-id')
            swal({
                title: '确定审核通过？',
                text: "审核通过后无法编辑和删除！",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText: '取消'
            }).then(function(isConfirm) {
                if(isConfirm.value == true){
                    axios({
                        method: 'post',
                        url: '/admin/api/package/review/'+id,
                    }).then(response => {
                        console.log(response);
                        if (response.data.status) {
                            swal(
                                "SUCCESS",
                                '成功！',
                                'success'
                            ).then(function () {
                                location.reload()
                            });
                        }else{
                            toastr.error(response.data.data.message);
                        }

                    }).catch(error => {
                        toastr.error(error.response.data.message);
                    });
                }
            })
        })
    })
</script>
