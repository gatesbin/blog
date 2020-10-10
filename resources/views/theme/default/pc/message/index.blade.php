@extends('theme.default.pc.frame')

@section('pageTitleMain','留言')

@section('bodyScript')
    @parent
    <script>
        var __app = {
            success:function () {
                var $comment = $('.comment');
                $comment.find('[name=username]').val('');
                $comment.find('[name=content]').val('');
                $comment.find('[name=email]').val('');
                $comment.find('[name=url]').val('');
                window.MZCaptcha.get('captcha').reset();
                window.api.dialog.tipSuccess('留言已经提交成功');
            }
        };
        (function () {
            $('[data-up]').on('click',function () {
                var $me = $(this);
                window.api.dialog.loadingOn();
                $.post('/message/up/'+$(this).attr('data-up'),function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res,{success:function (res) {
                        $me.find('span').html(res.data.count);
                        $me.addClass('active');
                    }});
                });
                return false;
            });
            $('[data-down]').on('click',function () {
                var $me = $(this);
                window.api.dialog.loadingOn();
                $.post('/message/down/'+$(this).attr('data-down'),function (res) {
                    window.api.dialog.loadingOff();
                    window.api.base.defaultFormCallback(res,{success:function (res) {
                        $me.find('span').html(res.data.count);
                        $me.addClass('active');
                    }});
                });
                return false;
            });
        })();
    </script>
@endsection

@section('bodyContent')

    <div class="comment page-block">
        <form action="/message/submit" method="post" data-ajax-form>
            <div class="head">
                <a href="javascript:;" class="avatar">
                    <img src="@assets('assets/lib/img/avatar.jpg')" />
                </a>
                <div class="input">
                    <input type="text" name="username" placeholder="输入你的称呼" />
                </div>
            </div>
            <div class="body">
                <textarea name="content" placeholder="输入想说的话"></textarea>
            </div>
            <div class="contact">
                <div class="uk-grid">
                    <div class="uk-width-medium-1-5">
                        <input type="text" name="email" placeholder="Email地址" />
                    </div>
                    <div class="uk-width-medium-1-5">
                        <input type="text" name="url" placeholder="主页或微博" />
                    </div>
                    <div class="uk-width-medium-1-5">
                        <img src="/message/submit_captcha?{{time()}}" data-captcha onclick="this.src='/message/submit_captcha?'+Math.random();" style="height:48px;border:1px solid #DDD;border-radius:3px;width:100%;" />
                    </div>
                    <div class="uk-width-medium-1-5">
                        <input type="text" name="captcha" placeholder="图片验证" />
                    </div>
                    <div class="uk-width-medium-1-5">
                        <button type="submit">提交</button>
                    </div>
                </div>
            </div>
            <div class="desc">
                <strong>隐私说明：</strong>你个人主页网址会被公开链接，但 Email 地址不会被公开显示。
            </div>
        </form>
    </div>

    <div class="comment-list">
        @foreach($messages as $message)
            <section class="item page-block">
                <div class="head">
                    <div class="avatar">
                        <img src="@assets('assets/lib/img/avatar.jpg')" />
                    </div>
                    @if(empty($message['username']))
                        <h3 class="uk-text-muted">匿名</h3>
                    @else
                        <h3>
                            @if($message['url'])
                                <a href="{{$message['url']}}" target="_blank">{{$message['username']}}</a>
                            @else
                                {{$message['username']}}
                            @endif
                        </h3>
                    @endif
                    <h4><time datetime="{{$message['created_at']}}"></time>说：</h4>
                </div>
                <div class="content">
                    {!! \TechSoft\Laravel\Util\HtmlUtil::text2html($message['content']) !!}
                </div>
                @if(!empty($message['reply']))
                    <div class="reply">
                        <p class="uk-text-muted">回复：</p>
                        {!! \TechSoft\Laravel\Util\HtmlUtil::text2html($message['reply']) !!}
                    </div>
                @endif
                <div class="action">
                    <a href="javascript:;" @if(\Illuminate\Support\Facades\Session::has('message-up-' . $message['id'])) class="active" @endif data-up="{{$message['id']}}"><i class="uk-icon-thumbs-o-up"></i> 赞同（<span>{{$message['upCount'] or 0}}</span>）</a>
                    <a href="javascript:;" @if(\Illuminate\Support\Facades\Session::has('message-down-' . $message['id'])) class="active" @endif data-down="{{$message['id']}}"><i class="uk-icon-thumbs-o-down"></i> 反对（<span>{{$message['downCount'] or 0}}</span>）</a>
                </div>
            </section>
        @endforeach
    </div>

    <div class="page-container">
        {!! $pageHtml !!}
    </div>

@endsection
