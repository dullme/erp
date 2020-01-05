<div class="modal fade " id="add_contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    &times;
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    添加企业联系人
                </h4>
            </div>
            <form action="/admin/contact" method="post" class="form-horizontal">
                <div class="modal-body">
                    <div class="box-header">
                        企业：
                        <span id="company_name" style="font-size: 20px"></span>
                        <input type="hidden" name="company_name" value="">
                    </div>
                    <div class="box-body" style="margin-top: 10px">
                        <div class="fields-group">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-5 {!! !$errors->has('name') ?: 'has-error' !!}">
                                        <label class="col-sm-4 control-label">姓名</label>
                                        <div class="col-sm-8">
                                            @if($errors->has('name'))
                                                <label class="control-label" for="inputError">
                                                    <i class="fa fa-times-circle-o"></i>{{ $errors->first('name') }}
                                                </label><br>
                                            @endif
                                            <div class="input-group ">
                                                <span class="input-group-addon"><i class="fa fa fa-pencil"></i></span>
                                                <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="请输入联系人姓名">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 {!! !$errors->has('mobile') ?: 'has-error' !!}">
                                        <label class="col-sm-4 control-label">联系电话</label>
                                        <div class="col-sm-8">
                                            @if($errors->has('mobile'))
                                                <label class="control-label" for="inputError">
                                                    <i class="fa fa-times-circle-o"></i>{{ $errors->first('mobile') }}
                                                </label><br>
                                            @endif
                                            <div class="input-group ">
                                                <span class="input-group-addon"><i class="fa fa fa-pencil"></i></span>
                                                <input type="text" name="mobile" value="{{ old('mobile') }}" class="form-control" placeholder="请输入联系电话">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-5 {!! !$errors->has('idcard') ?: 'has-error' !!}">
                                        <label class="col-sm-4 control-label">身份证号</label>
                                        <div class="col-sm-8">
                                            @if($errors->has('idcard'))
                                                <label class="control-label" for="inputError">
                                                    <i class="fa fa-times-circle-o"></i>{{ $errors->first('idcard') }}
                                                </label><br>
                                            @endif
                                            <div class="input-group ">
                                                <span class="input-group-addon"><i class="fa fa fa-pencil"></i></span>
                                                <input type="idcard" name="idcard" value="{{ old('idcard') }}" class="form-control" placeholder="请输入身份证号码">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 {!! !$errors->has('email') ?: 'has-error' !!}">
                                        <label class="col-sm-4 control-label">邮箱</label>
                                        <div class="col-sm-8">
                                            @if($errors->has('email'))
                                                <label class="control-label" for="inputError">
                                                    <i class="fa fa-times-circle-o"></i>{{ $errors->first('email') }}
                                                </label><br>
                                            @endif
                                            <div class="input-group ">
                                                <span class="input-group-addon"><i class="fa fa fa-pencil"></i></span>
                                                <input type="text" name="email" value="{{ old('email') }}" class="form-control" placeholder="请输入邮箱地址">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->

                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <input type="hidden" name="user_id" value="" id="user_id"/>
                    <button type="button" class="btn btn-default" data-dismiss="modal">关闭
                    </button>
                    <button type="submit" class="btn btn-primary">
                        提交更改
                    </button>
                </div>
            </form>

        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/javascript">
    function centerModals() {
        $('#add_contact').each(function(i) {
            var $clone = $(this).clone().css('display','block').appendTo('body');
            var top = Math.round(($clone.height() - $clone.find('.modal-content').height()) / 2);
            top = top > 0 ? top : 0;
            $clone.remove();
            $(this).find('.modal-content').css("margin-top", top);
        });
    };

    $('#add_contact').on('show.bs.modal', centerModals);

    $(window).on('resize', centerModals);

    function addContact(user_id,company_name){
        $("#user_id").val(user_id);
        $("#company_name").html(company_name);
        $("input[name='company_name']").val(company_name);
    }

    $(function(){
        var error = "{{ $errors->count() }}";
        if (error>0){
            addContact('{{ old('user_id') }}', '{{ old('company_name') }}');
            $('#add_contact').modal('show');
        }

    });


</script>
