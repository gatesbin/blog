<div class="pb">
    <div class="pb-member-message-list">
        <div class="action">
            <a href="javascript:;" class="uk-button" data-mark-read>标为已读</a>
            <a href="javascript:;" class="uk-button" data-mark-read-all>全部标为已读</a>
        </div>
        <table class="uk-table">
            <thead>
            <tr>
                <th width="10">&nbsp;</th>
                <th width="10">&nbsp;</th>
                <th>内容</th>
                <th width="200">时间</th>
            </tr>
            </thead>
            <tbody>
            <tr data-message-empty @if(!empty($records)) style="display:none;" @endif>
                <td colspan="4">
                    <div class="empty">
                        没有任何未读消息~
                    </div>
                </td>
            </tr>
            @foreach($records as $message)
                <tr class="unread">
                    <td><input type="checkbox" data-id="{{$message['id']}}" /></td>
                    <td><span class="dot"></span></td>
                    <td>
                        <div class="message">
                            {!! $message['content'] !!}
                        </div>
                    </td>
                    <td>
                        <div class="time">{{$message['createTime']}}</div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>