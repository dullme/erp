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
})
