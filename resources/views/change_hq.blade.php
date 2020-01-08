<div class="modal fade " id="change_hq" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    HQ
                </h4>
            </div>
            <form class="form-horizontal" id="myForm">
                <div class="modal-body">
                    <div class="box-body" style="margin-top: 10px">
                        <p><select id="hq_value" class="form-control" >
                                <option value="28" {{ session('hq', config('hq')) == 28 ?'selected="selected"' :'' }}>20</option>
                                <option value="60" {{ session('hq', config('hq')) == 60 ?'selected="selected"' :'' }}>40</option>
                                <option value="65" {{ session('hq', config('hq')) == 65 ?'selected="selected"' :'' }}>40HQ</option>
                            </select></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">修改</button>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>


<div class="modal fade " id="change_unit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    单位
                </h4>
            </div>
            <form class="form-horizontal" id="unitForm">
                <div class="modal-body">
                    <div class="box-body" style="margin-top: 10px">
                        <p><select id="unit_value" class="form-control" >
                                <option value="cm" {{ session('unit', config('unit')) == 'cm' ?'selected="selected"' :'' }}>厘米</option>
                                <option value="in" {{ session('unit', config('unit')) == 'in' ?'selected="selected"' :'' }}>英寸</option>
                            </select></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">修改</button>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    $(function(){
        $('#hq_value').select2();

        $('#myForm').submit(function (event) {
            event.preventDefault();

            axios({
                method: 'post',
                url: '/admin/api/change-hq',
                data: {
                    hq:$('#hq_value').val(),
                }
            }).then(response => {

                if(response.data){
                    swal(
                        "SUCCESS",
                        '修改成功！',
                        'success'
                    ).then(function () {
                        location.reload()
                    });
                }else{
                    swal(
                        "INFO",
                        '修改失败！',
                        'info'
                    )
                }
            }).catch(error => {
                swal(
                    "INFO",
                    '出错啦！',
                    'info'
                )
            });

            return false;
            // 直接在事件处理程序中返回false
        });

        $('#unit_value').select2();

        $('#unitForm').submit(function (event) {
            event.preventDefault();

            axios({
                method: 'post',
                url: '/admin/api/change-unit',
                data: {
                    unit:$('#unit_value').val(),
                }
            }).then(response => {

                if(response.data){
                    swal(
                        "SUCCESS",
                        '修改成功！',
                        'success'
                    ).then(function () {
                        location.reload()
                    });
                }else{
                    swal(
                        "INFO",
                        '修改失败！',
                        'info'
                    )
                }
            }).catch(error => {
                swal(
                    "INFO",
                    '出错啦！',
                    'info'
                )
            });

            return false;
            // 直接在事件处理程序中返回false
        });

    });


</script>
