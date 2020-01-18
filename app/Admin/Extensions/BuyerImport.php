<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class BuyerImport
{
    public function script()
    {
        return <<<SCRIPT
        $('#touch').unbind('click').click(function(){
            $('#user_info').click();
            $('#user_info').change(function(){
                var formData = new FormData();
                formData.append("file",$("#user_info")[0].files[0]);
                formData.append("_token",LA.token);

                $.ajax({
                    type: "post",
                    url: "/admin/api/buyer-import",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    beforeSend: function(){
                        $("#load").html('<i class="fa fa-spinner fa-pulse"></i>');
                    },
                    success: function(result){
                        if(result.status){
                            toastr.success('导入成功');
                        }else{
                            toastr.error(result.message);
                        }
                        $("#load").html('<span class="caret"></span><span class="sr-only">Toggle Dropdown</span>');
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown){
                        // console.log(XMLHttpRequest);
                        // console.log(textStatus);
                        // console.log(errorThrown);
                        toastr.error(XMLHttpRequest.responseText);
                    }
                });

                var obj = document.getElementById('user_info') ;
                obj.outerHTML=obj.outerHTML;
            });

        });
SCRIPT;

    }


    public function render()
    {
        Admin::script($this->script());

        return "<div class=\"btn-group \" style=\"margin-right: 10px\">
                    <a class=\"btn btn-sm btn-danger\" title='导入'>
                        <i class=\"fa fa-upload\"></i>
                        <span class=\"hidden-xs\">导入</span></a>
                    <button id='load' type=\"button\" class=\"btn btn-sm btn-danger dropdown-toggle\" data-toggle=\"dropdown\">
                        <span class=\"caret\"></span>
                        <span class=\"sr-only\">Toggle Dropdown</span>
                    </button>
                    <ul class=\"dropdown-menu\" role=\"menu\">
                        <li><a href=\"".asset('/template/template-company.xlsx')."\" target=\"_blank\">下载模板</a></li>
                        <li><a id='touch' style='display: block;padding: 3px 20px;clear: both;font-weight: 400;line-height: 1.42857143;white-space: nowrap;cursor: pointer;color: #777;' >出口商导入</a><input id='user_info' type='file' name='user_info' accept='.xls, .xlsx' style='display: none'></li>
                    </ul>
               </div>";
    }

    public function __toString()
    {
        return $this->render();
    }
}
