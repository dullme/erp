<template>
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">创建</h3>

            <div class="box-tools">
                <div class="btn-group pull-right" style="margin-right: 5px">
                    <a href="/admin/orders" class="btn btn-sm btn-default" title="列表"><i
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
                        <div class="form-group " :class="{'has-error': this.errors.has('lading_number')}">
                            <label class="col-sm-2 asterisk control-label">提单号</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('lading_number')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('lading_number') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input v-model="form_data.lading_number" type="text" name="lading_number" class="form-control" placeholder="输入 提单号">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('agreement_no')}">
                            <label class="col-sm-2 asterisk control-label">合同号</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('agreement_no')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('agreement_no') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input v-model="form_data.agreement_no" name="agreement_no" type="text" class="form-control" placeholder="输入 合同号">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('container_number')}">
                            <label class="col-sm-2  control-label">集装箱号</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('container_number')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('container_number') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input v-model="form_data.container_number" type="text" class="form-control" placeholder="输入 集装箱号">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('seal_number')}">
                            <label class="col-sm-2  control-label">铅封号</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('seal_number')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('seal_number') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input v-model="form_data.seal_number" type="text" class="form-control" placeholder="输入 铅封号">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('forwarding_company_id')}">
                            <label class="col-sm-2 asterisk control-label">货代公司</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('forwarding_company_id')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('forwarding_company_id') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <select class="form-control" id="forwarding_company" v-model="form_data.forwarding_company_id"> </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('packaged_at')}">
                            <label class="col-sm-2 asterisk control-label">发货日</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('packaged_at')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('packaged_at') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input id="packaged_at" v-model="form_data.packaged_at" type="text" class="form-control datetime-picker" placeholder="发货日">
                                </div>
                            </div>
                        </div>

                        <div class="form-group "  :class="{'has-error': this.errors.has('ship_port')}">
                            <label class="col-sm-2 asterisk control-label">发货港</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('ship_port')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('ship_port') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <select class="form-control select2" id="ship_port">
                                        <option value="">请选择</option>
                                        <option v-for="port in ship_ports " :value="port.name">{{ port.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('departure_at')}">
                            <label class="col-sm-2 asterisk control-label">预计离港时间</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('departure_at')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('departure_at') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input id="departure_at"  v-model="form_data.departure_at" type="text" class="form-control datetime-picker" placeholder="预计离港时间">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('arrival_port')}">
                            <label class="col-sm-2 asterisk control-label">到货港</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('arrival_port')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('arrival_port') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <select class="form-control select2" id="arrival_port">
                                        <option value="">请选择</option>
                                        <option v-for="port in arrival_ports " :value="port.name">{{ port.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('arrival_at')}">
                            <label class="col-sm-2 asterisk control-label">预计到港时间</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('arrival_at')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('arrival_at') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input id="arrival_at" v-model="form_data.arrival_at" type="text" class="form-control datetime-picker" placeholder="预计到港时间">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('entry_at')}">
                            <label class="col-sm-2 asterisk control-label">预计入仓时间</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('entry_at')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('entry_at') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <input id="entry_at" v-model="form_data.entry_at" type="text" class="form-control datetime-picker" placeholder="预计入仓时间">
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('buyer_id')}">
                            <label class="col-sm-2 asterisk control-label">出口商</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('buyer_id')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('buyer_id') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <select class="form-control" id="buyer-select2" v-model="form_data.buyer_id"> </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('customer_id')}">
                            <label class="col-sm-2 asterisk control-label">进口商</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('customer_id')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('customer_id') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <select class="form-control" id="customer-select2" v-model="form_data.customer_id"> </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group " :class="{'has-error': this.errors.has('warehouse_company_id')}">
                            <label class="col-sm-2 asterisk control-label">仓储公司</label>
                            <div class="col-sm-8">
                                <label class="control-label" v-if="errors.has('warehouse_company_id')">
                                    <i class="fa fa-times-circle-o"></i> {{ errors.get('warehouse_company_id') }}
                                </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-pencil fa-fw"></i></span>
                                    <select class="form-control" id="warehouse-company-select2" v-model="form_data.warehouse_company_id"> </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label class="col-sm-2 control-label">备注</label>
                            <div class="col-sm-8">
                                <textarea class="form-control remark" v-model="form_data.remark" rows="5" placeholder="输入 备注"></textarea>
                            </div>
                        </div>


                        <div class="form-group  ">
                            <label class="col-sm-2  control-label">单品选择</label>
                            <div class="col-sm-8">
                                <table class="table table-hover" id="table-fields">
                                    <tbody>
                                    <tr>
                                        <th>单品</th>
                                        <th style="width: 100px">装箱数量</th>
                                        <th style="width: 100px">操作</th>
                                    </tr>
                                    <tr :id="'product_info'+product_info.id" v-for="(product_info) in form_data.product_info" :key="product_info.length">
                                        <td>
                                            <select class="form-control" :id="'product_id' + product_info.id" v-model="product_info.id"> </select>
                                        </td>
                                        <td>
                                            <input :id="'quantity'+product_info.id" type="text" class="form-control numeric" value="1"
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


                        <div class="form-group  ">
                            <label class="col-sm-2  control-label">赠品选择</label>
                            <div class="col-sm-8">
                                <table class="table table-hover" id="item-table-fields">
                                    <tbody>
                                    <tr>
                                        <th>赠品</th>
                                        <th style="width: 100px">赠品数量</th>
                                        <th style="width: 100px">操作</th>
                                    </tr>
                                    <tr :id="'item_info'+item_info.id" v-for="(item_info) in form_data.item_info" :key="item_info.length">
                                        <td>
                                            <select class="form-control" :id="'item_id' + item_info.id" v-model="item_info.id"> </select>
                                        </td>
                                        <td>
                                            <input :id="'quantity'+item_info.id" type="text" class="form-control numeric" value="1"
                                                   @keyup="item_info.quantity = $event.target.value" placeholder="数量" >
                                        </td>
                                        <td><a class="btn btn-sm btn-danger table-field-remove" @click="deleteItem(item_info.id)"><i class="fa fa-trash"></i> 删除</a></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <hr style="margin-top: 0;">
                                <div class="form-inline margin" style="width: 100%">
                                    <div class="form-group">
                                        <button type="button" @click="addItem" class="btn btn-sm btn-success" id="add-item-table-field">
                                            <i class="fa fa-plus"></i>&nbsp;&nbsp;添加
                                        </button>
                                        <span class="text-danger" id="item_info_message"></span>
                                        <span class="text-danger" v-if="this.errors.has('item_info')">{{ this.errors.get('item_info') }}</span>
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
                ship_ports:[],
                arrival_ports:[],
                form_data: {
                    agreement_no: '',
                    ship_port:'',
                    arrival_port:'',
                    lading_number: '',
                    container_number: '',
                    seal_number: '',
                    forwarding_company_id: '',
                    warehouse_company_id: '',
                    buyer_id: '',
                    customer_id: '',
                    packaged_at:Date.today().toString('yyyy-MM-dd'),
                    departure_at:Date.today().toString('yyyy-MM-dd'),
                    arrival_at:Date.today().toString('yyyy-MM-dd'),
                    entry_at:Date.today().toString('yyyy-MM-dd'),
                    remark:'',
                    images: [],  //图片
                    product_info:[], //单品
                    item_info:[], //赠品
                },
                product_info_count:0,
                info_length:0,
                item_info_count:0,
                item_info_length:0,
                edit_data: {
                    product:[],
                    forwarding_company_id:'',
                    initialPreview: [],
                    initialPreviewConfig: [],
                }
            }
        },

        watch: {
            product_info_count(newVal, oldVal){
                if(newVal > oldVal){
                    this.productInfoSelect2('l-'+this.info_length)
                }
            },
            item_info_count(newVal, oldVal){
                if(newVal > oldVal){
                    this.itemInfoSelect2('il-'+this.item_info_length)
                }
            }
        },

        created() {
            axios.get('/admin/api/port').then(response => {
                this.ship_ports = response.data.data.ship_port
                this.arrival_ports = response.data.data.arrival_port
            });

            console.log('created')
        },

        mounted() {
            console.log('mounted')
            $('.datetime-picker').datetimepicker({
                'format': 'YYYY-MM-DD',
                'locale': 'zh-CN',
                'allowInputToggle': true
            });
            $('#packaged_at').on('dp.change', (e) => {
                this.form_data.packaged_at = e.currentTarget.value;
            });
            $('#departure_at').on('dp.change', (e) => {
                this.form_data.departure_at = e.currentTarget.value;
            });
            $('#arrival_at').on('dp.change', (e) => {
                this.form_data.arrival_at = e.currentTarget.value;
            });
            $('#entry_at').on('dp.change', (e) => {
                this.form_data.entry_at = e.currentTarget.value;
            });

            $('#ship_port').select2().on("change", () => {
                this.form_data.ship_port = $("#ship_port").val()
                this.errors.clear('ship_port')
            });
            $('#arrival_port').select2().on("change", () => {
                this.form_data.arrival_port = $("#arrival_port").val()
                this.errors.clear('arrival_port')
            });

            this.supplierSelect2()
            this.buyerSelect2()
            this.customerSelect2()
            this.warehouseCompanySelect2()
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

            deleteItem(length){
                console.log(length)
                this.errors.clear('item_info')
                this.form_data.item_info.forEach((value, index)=>{
                    if(value.id == length){
                        $('#item_info'+value.id).remove()
                        this.form_data.item_info[index].deleted = true;
                        // this.$delete(this.form_data.product_info, index)
                    }
                })
                // this.form_data.product_info.forEach((value)=>{
                //     this.productInfoSelect2(value.id)
                // })
                this.item_info_count = this.form_data.item_info.length
            },

            supplierSelect2(){
                Common.select(this.edit_data.supplier_id, "#forwarding_company", "/admin/api/forwarding-company", "name", "mobile", true, '请选择货代公司');
                $("#forwarding_company").on("change", () => {
                    this.form_data.forwarding_company_id = parseInt($("#forwarding_company").val());
                    this.errors.clear('forwarding_company_id')
                });
            },

            buyerSelect2(){
                Common.select([], "#buyer-select2", "/admin/api/buyer", "name", "mobile", true, '请选择出口商');
                $("#buyer-select2").on("change", () => {
                    this.form_data.buyer_id = parseInt($("#buyer-select2").val());
                    this.errors.clear('buyer_id')
                });
            },

            customerSelect2(){
                Common.select([], "#customer-select2", "/admin/api/customer", "name", "mobile", true, '请选择进口商');
                $("#customer-select2").on("change", () => {
                    this.form_data.customer_id = parseInt($("#customer-select2").val());
                    this.errors.clear('customer_id')
                });
            },

            warehouseCompanySelect2(){
                Common.select([], "#warehouse-company-select2", "/admin/api/warehouse-company2", "name", "mobile", true, '请选择仓储公司');
                $("#warehouse-company-select2").on("change", () => {
                    this.form_data.warehouse_company_id = parseInt($("#warehouse-company-select2").val());
                    this.errors.clear('warehouse_company_id')
                });
            },

            productInfoSelect2(index){
                this.$nextTick( ()=> {
                    Common.select(this.edit_data.product, '#product_id' + index, "/admin/api/can-box", "name", "text", true, '请选输入关键字', 1, 'zh-CN',
                        function (repo) {
                            if (repo.loading) return '搜索中...';
                            let image = repo['image'] ? "/uploads/"+repo['image'] : 'http://erp.test/vendor/laravel-admin/AdminLTE/dist/img/user2-160x160.jpg'
                            let html =
                                "<div style='display: flex'>" +
                                "<div><img width='50px' height='50px' src='"+image+"'></div>" +
                                "<div style='margin-left: 20px'>" +
                                "<div>库存：" + repo['warehouses_count'] + "</div>" +
                                "<div>SKU：" + repo['sku'] + "</div>" +
                                "<div>DDP：" + repo['ddp'] + "</div>" +
                                "<div>描述：" + repo['description'] + "</div>" +
                                "</div>" +
                                "</div>";
                            return html;
                        },
                        function (repo) {
                            let text = repo.sku;

                            if(repo['warehouses_count']){
                                text += '【库存:'+repo['warehouses_count']+'】';
                            }

                            if (repo['description']) {
                                let description = repo['description'].length > 20 ? repo['description'].substr(0,20) + '...' : repo['description'];

                                text += '：' + description;
                            }

                            return text
                        }
                        , false);

                    $('#product_id' + index).on('change', (e) => {
                        this.errors.clear('product_info')
                        this.form_data.product_info.forEach((value, key)=>{

                            if(value.id == index){
                                this.$set(this.form_data.product_info[key], 'product_id', e.target.value);
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

                    // if(last_product_info['price'] <=0){
                    //     this.setInfoMessage('product_info', '单价必须大于0')
                    //     return false;
                    // }
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

            itemInfoSelect2(index){
                this.$nextTick( ()=> {
                    Common.select([], '#item_id' + index, "/admin/api/items", "name", "unit", true, '请选择赠品');

                    $('#item_id' + index).on('change', (e) => {
                        this.errors.clear('item_info')
                        this.form_data.item_info.forEach((value, key)=>{

                            if(value.id == index){
                                this.$set(this.form_data.item_info[key], 'item_id', e.target.value);
                            }
                        })
                    });

                    $('#item_info' + index + ' .numeric').inputmask({
                        "alias": "integer",
                    });
                })
            },

            addItem(){
                this.errors.clear('item_info')
                let last_item_info = this.form_data.item_info[this.form_data.item_info.length -1]
                if(last_item_info && last_item_info['deleted'] == false){
                    if(!last_item_info['item_id']){
                        this.setInfoMessage('item_info', '请选择赠品')
                        return false;
                    }

                    if(last_item_info['quantity'] <=0){
                        this.setInfoMessage('item_info', '数量必须大于0')
                        return false;
                    }

                    // if(last_product_info['price'] <=0){
                    //     this.setInfoMessage('product_info', '单价必须大于0')
                    //     return false;
                    // }
                }

                this.form_data.item_info.push({
                    id:'il-' + (++this.item_info_length),
                    item_id:'',
                    quantity:1,
                    deleted:false
                });

                this.item_info_count = this.form_data.item_info.length
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
                    }else if(i == 'item_info'){
                        for(let i=0,len=this.form_data.item_info.length;i<len;i++){
                            form_data.append('item_info['+i+'][item_id]',this.form_data.item_info[i].item_id)
                            form_data.append('item_info['+i+'][quantity]',this.form_data.item_info[i].quantity)
                            form_data.append('item_info['+i+'][deleted]',this.form_data.item_info[i].deleted)
                        }
                    }else{
                        form_data.append(i, this.form_data[i]);
                    }
                }

                let url = '/admin/packages'

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
                            location.href = '/admin/packages';
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
</script>

<style>
    .select2-container{
        width: 100% !important;
    }
</style>
