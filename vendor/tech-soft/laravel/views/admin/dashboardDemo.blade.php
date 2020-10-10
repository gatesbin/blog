@if (env('ADMIN_DEMO_USER_ID', 0) && $_adminUserId == env('ADMIN_DEMO_USER_ID', 0))
    <div class="uk-alert uk-alert-danger">
        当前账号为 <strong>演示账号</strong>，不能进行 增加/编辑/删除 操作。
    </div>
@endif