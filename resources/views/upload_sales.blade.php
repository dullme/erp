<div class="modal fade " id="upload-sales" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    HQ-123
                </h4>
            </div>
            <form class="form-horizontal" id="myForm">
                <div class="modal-body">
                    <div class="box-body" style="margin-top: 10px">
                        <p>
                            <select id="ware_com" class="form-control" >
                                @foreach($warehouse_company as $key=>$item)
                                    <option value="{{ $key }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </p>
                        <p><input type="file" id="upload-file" onchange="upload(this)"></p>
                    </div>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    function upload(obj){
        var files = obj.files ;
        var formData = new FormData();
        formData.append("file", files[0]);
        formData.append('id', $('#ware_com').val());

        axios({
            method: 'post',
            url: '/admin/api/sales-profit-import',
            data: formData
        }).then(response => {

            if(response.data){
                if(response.data.status){
                    swal(
                        "SUCCESS",
                        '导入成功！',
                        'success'
                    ).then(function () {
                        location.reload()
                    });
                }else{
                    swal(
                        "INFO",
                        response.data.message,
                        'info'
                    ).then(function () {
                        location.reload()
                    })
                }


            }else{
                swal(
                    "INFO",
                    '修改失败！',
                    'info'
                ).then(function () {
                    location.reload()
                })
            }
        }).catch(error => {
            swal(
                "INFO",
                '出错啦！',
                'info'
            ).then(function () {
                location.reload()
            })
        });
    }

    $(function(){
        $('#ware_com').select2();
    });


</script>
