<template>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">创建</h3>

            <div class="box-tools">
                <div class="btn-group pull-right" style="margin-right: 5px">
                    <a href="/admin/composes" class="btn btn-sm btn-default" title="列表"><i
                        class="fa fa-list"></i><span class="hidden-xs">&nbsp;列表</span></a>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <!-- form start -->
        <form class="form-horizontal" @submit.prevent="submit" @keydown="errors.clear($event.target.name)">

            <div class="box-body">

                <div class="fields-group">

                    <div class="col-md-12">
                        <div class="form-group  ">
                            <label class="col-sm-2  control-label">组合名称</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input v-model="form_data.name" type="text" class="form-control" placeholder="输入 组合名称">
                                </div>
                            </div>
                        </div>

                        <div class="form-group  ">
                            <label class="col-sm-2  control-label">ASIN</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input v-model="form_data.asin" type="text" class="form-control" placeholder="输入 ASIN">
                                </div>
                            </div>
                        </div>

                        <div class="form-group" :class="{'has-error': this.errors.has('images')}">
                            <label class="col-sm-2  control-label">文件上传</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('images')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('images') }}
                                </label>
                                <input v-on:change="form_data.images" type="file" name="images[]"
                                       class="file-loading pictures" id="input-id" multiple="1"/>
                            </div>
                        </div>


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

    import Errors from './core/Errors'

    export default {
        data() {
            return {
                errors: new Errors(),
                form_data: {
                    name: '222',
                    asin: '333',
                    images: [],  //图片
                },
                edit_data: {
                    initialPreview: [],
                    initialPreviewConfig: [],
                }
            }
        },

        created() {
            console.log('created')
        },

        mounted() {
            console.log('mounted')
            this.pictures() //上传图片
        },

        methods: {
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
                console.log(this.name)
                console.log(this.asin)
            }

        }
    }
</script>
