<div class="modal fade " id="add_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title">
                    快速生成组合
                </h4>
            </div>
            <form class="form-horizontal" id="product-compose-form">
                <div class="modal-body">
                    <div class="box-body" style="margin-top: 10px">

                        <div class="form-group">
                            <label for="sku" class="col-sm-2  control-label">单品描述</label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <span class="form-control" id="product-name"></span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sku" class="col-sm-2  control-label">组合名称</label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input id="compose-product" class="form-control" type="text" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sku" class="col-sm-2  control-label">ASKU</label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input id="compose-product-asin" class="form-control" type="text" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sku" class="col-sm-2  control-label">数量</label>
                            <div class="col-sm-8">
                                <div class="input-group" style="width: 100%">
                                    <input id="compose-product-quantity" class="form-control" type="number" value="1"/>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">生成组合</button>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    var product_id = null;

    function addProduct(id, name, sku){
        product_id = id;
        $('#product-name').html(name)
        $('#compose-product').val(name)
        $('#compose-product-asin').val(sku)
    }

    $(function(){
        $('#product-compose-form').submit(function (event) {
            event.preventDefault();

            if(product_id!=null){
                axios({
                    method: 'post',
                    url: '/admin/api/compose',
                    data: {
                        id:product_id,
                        name:$('#compose-product').val(),
                        asin:$('#compose-product-asin').val(),
                        quantity:$('#compose-product-quantity').val()
                    }
                }).then(response => {

                    if(response.data){
                        swal(
                            "SUCCESS",
                            '组合创建成功！',
                            'success'
                        ).then(function () {
                            location.reload()
                        });
                    }else{
                        swal(
                            "INFO",
                            '组合创建失败！',
                            'info'
                        )
                    }
                }).catch(error => {
                    let text = '出错啦！';
                    if(error.response.data.errors.name){
                        text= error.response.data.errors.name[0]
                    }else if(error.response.data.errors.asin){
                        text= error.response.data.errors.asin[0]
                    }else if(error.response.data.errors.quantity){
                        text= error.response.data.errors.quantity[0]
                    }else if(error.response.data.errors.id){
                        text= error.response.data.errors.id[0]
                    }
                    console.log(error.response.data.errors)
                    swal(
                        "INFO",
                        text,
                        'info'
                    )
                });
            }

            return false;
            // 直接在事件处理程序中返回false
        });
    });



</script>
