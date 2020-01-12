<template>
    <div>
        <form class="form-horizontal" @submit.prevent="submit" @keydown="errors.clear($event.target.name)">
            <div class="box-body">
                <div class="fields-group">

                    <div class="col-md-12">
                        <div class="form-group ">
                            <label class="col-sm-2  control-label">订单编号</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="form-control">{{ no }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('entry_at')}">
                            <label class="col-sm-2  control-label">入库时间</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('entry_at')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('entry_at') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input id="entry_at" v-model="form_data.entry_at" type="text" class="form-control datetime-picker" placeholder="入库时间">
                                </div>
                            </div>
                        </div>


                        <div class="form-group  ">
                            <label class="col-sm-2  control-label">单品选择</label>
                            <div class="col-sm-10">
                                <table class="table table-hover" id="table-fields">
                                    <tbody>
                                    <tr>
                                        <th>单品</th>
                                        <th style="width: 100px">数量</th>
                                        <th style="width: 100px">操作</th>
                                    </tr>
                                    <tr :id="'product_info'+product_info.id" v-for="(product_info) in form_data.product_info" :key="product_info.length">
                                        <td>
                                            <select class="form-control" :id="'product_id' + product_info.id" v-model="product_info.id"> </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control numeric" value="1"
                                                   @keyup="product_info.quantity = $event.target.value" placeholder="数量" >
                                        </td>
                                        <td><a class="btn btn-sm btn-danger table-field-remove" @click="deleteproduct(product_info.id)"><i class="fa fa-trash"></i> 删除</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <hr style="margin-top: 0;">
                                <div class="form-inline margin" style="width: 100%">
                                    <div class="form-group">
                                        <button type="button" @click="addproduct" class="btn btn-sm btn-success" id="add-table-field">
                                            <i class="fa fa-plus"></i>&nbsp;&nbsp;添加
                                        </button>
                                        <span class="text-danger" id="product_info_message"></span>
                                        <span class="text-danger" v-if="this.errors.has('product_info')">{{ this.errors.get('product_info') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!--                        <div class="form-group" :class="{'has-error': this.errors.has('images')}">-->
                        <!--                            <label class="col-sm-2  control-label">文件上传</label>-->
                        <!--                            <div class="col-sm-8">-->
                        <!--                                <label class="control-label" v-if="errors.has('images')">-->
                        <!--                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('images') }}-->
                        <!--                                </label>-->
                        <!--                                <input v-on:change="form_data.images" type="file" name="images[]"-->
                        <!--                                       class="file-loading pictures" id="input-id" multiple="1"/>-->
                        <!--                            </div>-->
                        <!--                        </div>-->




                    </div>
                </div>

            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="btn-group pull-right">
                        <button type="submit" class="btn btn-primary">提交</button>
                    </div>
                    <div class="btn-group pull-left">
                        <button type="reset" class="btn btn-warning">重置</button>
                    </div>
                </div>
            </div>

            <!-- /.box-footer -->
        </form>
    </div>
</template>

<script>
    import Common from '../util'
    import Errors from './core/Errors'

    require('../../../public/vendor/datejs/date-zh-CN')

    export default {
        data() {
            return {
                errors: new Errors(),
                form_data: {
                    supplier_id: '',
                    entry_at:Date.today().toString('yyyy-MM-dd'),
                    images: [],  //图片
                    product_info:[], //单品
                },
                order_product:[],
                product_info_count:0,
                info_length:0,
                edit_data: {
                    product:[],
                    supplier_id:'',
                    initialPreview: [],
                    initialPreviewConfig: [],
                }
            }
        },

        props: [
            'order_id',
            'no',
            'product'
        ],

        watch: {
            product_info_count(newVal, oldVal){
                if(newVal > oldVal){
                    this.productInfoSelect2('l-'+this.info_length)
                }
            }
        },

        created() {
            if(this.product){
                this.order_product = JSON.parse(this.product);
            }
            console.log('created')
        },

        mounted() {
            console.log('mounted')
            $('.datetime-picker').datetimepicker({
                'format': 'YYYY-MM-DD',
                'locale': 'zh-CN',
                'allowInputToggle': true
            });
            $('#entry_at').on('dp.change', (e) => {
                this.form_data.entry_at = e.currentTarget.value;
            });
            this.supplierSelect2()
            this.pictures() //上传图片
        },

        methods: {
            deleteproduct(length){
                console.log(length)
                this.errors.clear('product_info')
                this.form_data.product_info.forEach((value, index)=>{
                    if(value.id == length){
                        $('#product_info'+value.id).remove()
                        this.form_data.product_info[index].deleted = true;
                        // this.$delete(this.form_data.product_info, index)
                    }
                })
                // this.form_data.product_info.forEach((value)=>{
                //     this.productInfoSelect2(value.id)
                // })
                this.product_info_count = this.form_data.product_info.length
            },

            supplierSelect2(){
                Common.select(this.edit_data.supplier_id, "#supplier", "/admin/api/supplier", "name", "mobile", true, '请选择供应商');
                $("#supplier").on("change", () => {
                    this.form_data.supplier_id = parseInt($("#supplier").val());
                });
            },

            productInfoSelect2(index){
                this.$nextTick( ()=> {
                    Common.select(this.edit_data.product, '#product_id' + index, "/admin/api/product?order_id="+this.order_id, "name", "text", true, '请选输入关键字', 1, 'zh-CN',
                        function (repo) {
                            if (repo.loading) return '搜索中...';
                            let image = repo['image'] ? "/uploads/"+repo['image'] : 'http://erp.test/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg'
                            let html =
                                "<div style='display: flex'>" +
                                "<div><img width='50px' height='50px' src='"+image+"'></div>" +
                                "<div style='margin-left: 20px'>" +
                                "<div>入库状态：" + repo['has'] + "/"+repo['needed']+"</div>" +
                                "<div>SKU：" + repo['text'] + "</div>" +
                                "<div>DDP：" + repo['ddp'] + "</div>" +
                                "<div>描述：" + repo['description'] + "</div>" +
                                "</div>" +
                                "</div>";
                            return html;
                        },
                        function (repo) {
                            if (repo['description']) {
                                let description = repo['description'].length > 20 ? repo['description'].substr(0,20) + '...' : repo['description'] + '【入库状态:'+repo['has']+'/'+repo['needed']+'】';

                                return repo['text'] + '：' + description;
                            }

                            return repo.text;
                        }
                        , false);

                    $('#product_id' + index).on('change', (e) => {
                        this.errors.clear('product_info')
                        this.form_data.product_info.forEach((value, key)=>{
                            if(value.id == index){
                                let id = e.target.value;
                                this.form_data.product_info[key]['product_id'] = id;
                            }
                        })
                    });

                    $('#product_info' + index + ' .numeric').inputmask({
                        "alias": "integer",
                    });
                })
            },

            addproduct(){
                this.errors.clear('product_info')
                let last_product_info = this.form_data.product_info[this.form_data.product_info.length -1]
                if(last_product_info && last_product_info['deleted'] == false){
                    if(!last_product_info['product_id']){
                        this.setInfoMessage('product_info', '请选择单品')
                        return false;
                    }

                    if(last_product_info['quantity'] <=0){
                        this.setInfoMessage('product_info', '数量必须大于0')
                        return false;
                    }
                }

                this.form_data.product_info.push({
                    id:'l-' + (++this.info_length),
                    product_id:'',
                    quantity:1,
                    deleted:false
                });

                this.product_info_count = this.form_data.product_info.length
                this.$nextTick( ()=> {
                    $(".decimal").inputmask({ alias: "decimal"});
                })

            },

            setInfoMessage(id,text){
                $('#'+id+'_message').html(text)
                setTimeout(function () {
                    $('#'+id+'_message').html('');
                },3000);
            },

            pictures() {
                $("input.pictures").fileinput({
                    language: 'zh',
                    overwriteInitial: false,
                    initialPreview: this.edit_data.initialPreview,
                    initialPreviewAsData: true,//默认标记
                    initialPreviewFileType: 'image', // image is the default and can be overridden in config below
                    initialPreviewConfig: this.edit_data.initialPreviewConfig,
                    deleteUrl: '/admin/agreement/delete/image',
                    showRemove: false,
                    showUpload: false,
                    allowedFileExtensions: ["jpg", "jpeg", "gif", "png"],   //允许上传的文件类型
                    previewFileType: "image",
                    browseLabel: "请选择..."
                }).on('change', (event) => {
                    this.form_data.images = event.target.files
                    this.errors.clear('images')
                }).on('filecleared', (event) => {
                    this.form_data.images = []
                }).on('filedeleted',function(event, key, jqXHR, data) {
                    if(jqXHR.responseJSON.data){
                        toastr.success('图片删除成功');
                    }else{
                        toastr.success('图片删除失败');
                    }
                });
            },

            submit(){
                let form_data = new FormData();
                for (let i in this.form_data) {
                    if (i == 'images') {
                        for (let j in this.form_data.images) {
                            form_data.append('images[]', this.form_data.images[j]);
                        }
                    }else if(i == 'product_info'){
                        for(let i=0,len=this.form_data.product_info.length;i<len;i++){
                            form_data.append('product_info['+i+'][product_id]',this.form_data.product_info[i].product_id)
                            form_data.append('product_info['+i+'][quantity]',this.form_data.product_info[i].quantity)
                            form_data.append('product_info['+i+'][deleted]',this.form_data.product_info[i].deleted)
                        }
                    }else{
                        form_data.append(i, this.form_data[i]);
                    }
                }

                form_data.append('order_id', this.order_id)

                let url = '/admin/api/warehouses'

                axios({
                    method: 'post',
                    url: url,
                    data: form_data
                }).then(response => {
                    console.log(response);
                    if (response.data.status) {
                        swal(
                            "SUCCESS",
                            response.data.message,
                            'success'
                        ).then(function () {
                            location.reload()
                        });
                    }else{
                        toastr.error(response.data.data.message);
                    }

                }).catch(error => {
                    if(error.response.data.status == false){
                        toastr.error(error.response.data.message);
                    }else{
                        this.errors.record(error.response.data.errors);
                    }
                });

            }

        }
    }

    $(function () {
        $('.preview').on('click', function () {
            let id = $(this).attr('data-id')
            swal({
                title: '确定入库？',
                text: "确认后无法更改！",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText: '取消'
            }).then(function(isConfirm) {
               if(isConfirm.value == true){
                   axios({
                       method: 'post',
                       url: '/admin/api/order-batch-confirm/'+id,
                   }).then(response => {
                       console.log(response);
                       if (response.data.status) {
                           swal(
                               "SUCCESS",
                               '入库成功！',
                               'success'
                           ).then(function () {
                               location.reload()
                           });
                       }else{
                           toastr.error(response.data.data.message);
                       }

                   })
               }
            })
        })


        $('.preview-delete').on('click', function () {
            let id = $(this).attr('data-id')
            swal({
                title: '确定删除？',
                text: "删除后无法恢复！",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText: '取消'
            }).then(function(isConfirm) {
                if(isConfirm.value == true){
                    axios({
                        method: 'post',
                        url: '/admin/api/order-batch-confirm/'+id+'/delete',
                    }).then(response => {
                        console.log(response);
                        if (response.data.status) {
                            swal(
                                "SUCCESS",
                                '删除成功！',
                                'success'
                            ).then(function () {
                                location.reload()
                            });
                        }else{
                            toastr.error(response.data.data.message);
                        }

                    })
                }
            })
        })

        $('#order-finish').on('click', function () {
            let id = $(this).attr('data-id')
            swal({
                title: '确定完结订单？',
                text: "订单完成后无法再次开启！",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText: '取消'
            }).then(function(isConfirm) {
                if(isConfirm.value == true){
                    axios({
                        method: 'post',
                        url: '/admin/api/order/finish/'+id,
                    }).then(response => {
                        console.log(response);
                        if (response.data.status) {
                            swal(
                                "SUCCESS",
                                '订单已完结！',
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

        $('#order-delete').on('click', function () {
            let id = $(this).attr('data-id')
            swal({
                title: '确定删除订单？',
                text: "订单删除后无法恢复！",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '确定',
                cancelButtonText: '取消'
            }).then(function(isConfirm) {
                if(isConfirm.value == true){
                    axios({
                        method: 'post',
                        url: '/admin/api/order/delete/'+id,
                    }).then(response => {
                        console.log(response);
                        if (response.data.status) {
                            swal(
                                "SUCCESS",
                                '删除成功！',
                                'success'
                            ).then(function () {
                                location.href="/admin/orders"
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

<style>
    .select2-container{
        width: 100% !important;
    }
</style>
