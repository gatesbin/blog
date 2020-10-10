<script>
    $(function () {
        $('[data-mark-read]').on('click',function () {
            var ids = [];
            $('[data-id]:checked').each(function (i,o) {
                ids.push($(o).attr('data-id'));
            });
            if(ids.length==0){
                window.api.dialog.tipError('请先选择消息');
                return;
            }
            window.api.dialog.loadingOn();
            $.post('/member/message_read',{ids:ids},function (res) {
                window.api.dialog.loadingOff();
                window.api.base.defaultFormCallback(res,{
                    success:function () {
                        $('[data-id]:checked').each(function (i,o) {
                            $(this).closest('tr').remove();
                        });
                        $('[data-member-unread-message-count]').html($('[data-id]').length);
                        if(!$('[data-id]').length){
                            $('[data-member-unread-message-count]').remove();
                            $('[data-message-empty]').show();
                        }
                    }
                });
            });
        });
        $('[data-mark-read-all]').on('click',function () {
            window.api.dialog.confirm('确定全部标记为已读?',function () {
                window.api.dialog.loadingOn();
                $.post('/member/message_read_all',{},function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res,{
                        success:function () {
                            $('[data-id]').each(function (i,o) {
                                $(this).closest('tr').remove();
                            });
                            $('[data-member-unread-message-count]').remove();
                            $('[data-message-empty]').show();
                        }
                    });
                });
            });
        });
    });
</script>