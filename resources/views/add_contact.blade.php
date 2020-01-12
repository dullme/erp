<div class="modal fade " id="add_contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    入库
                </h4>
            </div>
            <form class="form-horizontal" id="myForm">
                <div class="modal-body">
                    <div class="box-body" style="margin-top: 10px">
                        <p>提单号：<span id="lading_number"></span></p>
                        <p>集装箱号：<span id="container_number"></span></p>
                        <p>铅封号：<span id="seal_number"></span></p>
                        <p>货代公司：<span id="forwarding_company"></span></p>
                        <p>SKU:数量：<span id="sku-text"></span></p>
                        <p><select id="warehouse-companies" name="warehouse_companies" class="form-control" ></select></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">入库</button>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    var package_id = null;

    function addContact(id){
        package_id = id;
        axios.get('/admin/api/getPackage-info/'+ package_id).then(response =>{
            $('#lading_number').html(response.data.lading_number)
            $('#container_number').html(response.data.container_number)
            $('#seal_number').html(response.data.seal_number)
            $('#forwarding_company').html(response.data.forwarding_company.name)
            let text = '';
            for(let i=0; i<response.data.warehouse.length; i++){
                if(i != 0){
                    text += '|'
                }
                text += response.data.warehouse[i]['sku'] +':'+ response.data.warehouse[i]['quantity']
            }
            $('#sku-text').html(text)
        }).catch(error => {
            console.log(error.response.data);
        })
    }

    $(function(){
        axios.get('/admin/api/warehouse-company').then(response => {
            $("#warehouse-companies").select2({
                data:response.data,
                placeholder: '请选择'
            }).on("change", () => {
                $("#warehouse-companies").val();
            });
            console.log();
        }).catch(error => {
            console.log(error.response.data);
        });

        $('#myForm').submit(function (event) {
            event.preventDefault();

            if(package_id!=null){
                axios({
                    method: 'post',
                    url: '/admin/api/package-in',
                    data: {
                        id:package_id,
                        company:$('#warehouse-companies').val(),
                    }
                }).then(response => {

                    if(response.data.status){
                        swal(
                            "SUCCESS",
                            '入库成功！',
                            'success'
                        ).then(function () {
                            location.href='/admin/packages'
                        });
                    }else{
                        swal(
                            "INFO",
                            '入库失败！',
                            'info'
                        )
                    }
                }).catch(error => {
                    swal(
                        "INFO",
                        error.response.data.message,
                        'info'
                    )
                });
            }

            return false;
            // 直接在事件处理程序中返回false
        });
    });



</script>
