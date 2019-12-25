<div class="box box-info">
    <div class="box-header with-border">
        <h3 class="box-title">详情</h3>

        <div class="box-tools">
            <div class="btn-group pull-right" style="margin-right: 5px">
                <a href="{{ url('/admin/composes') }}" class="btn btn-sm btn-default" title="列表">
                    <i class="fa fa-list"></i><span class="hidden-xs"> 列表</span>
                </a>
            </div>
        </div>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <div class="row" style="margin-bottom: 20px">
            <div class="col-lg-3">
                @if($compose->image)
                    <img width="100%" src="{{ asset('uploads/'.$compose->image[0]) }}" class="img-rounded">
                @endif
            </div>
            <div class="col-lg-9">
                <p>组合名称：{{ $compose->name }}</p>
                <p>ASIN：{{ $compose->asin }}</p>
                <p>箱数：{{ $compose->composeProducts->sum('quantity') }}</p>
                <p>40HQ：{{ 65/$compose->composeProducts->sum('volume') }}</p>
                <p>DDP：{{ $compose->composeProducts->sum('ddp') }}</p>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">单品</div>
            <table class="table table-bordered text-center">
                <thead>
                    <tr>
                        <th>图片</th>
                        <th>描述</th>
                        <th>SKU</th>
                        <th>数量</th>
                        <th>体积</th>
                        <th>DDP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compose->composeProducts as $item)
                        <tr>
                            <td>
                                @if($item->product->image)
                                    <img width="100" src="{{ asset('uploads/'.$item->product->image) }}">
                                @endif
                            </td>
                            <td>
                                <span data-toggle="tooltip" data-placement="top" title="{{ $item->product->description }}">
                                    {{ mb_substr($item->product->description, 0 , 20) }}
                                </span>
                            </td>
                            <td>{{ $item->product->sku }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->volume }}</td>
                            <td>{{ $item->product->ddp }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
