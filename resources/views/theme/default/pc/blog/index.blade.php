@extends('theme.default.pc.frame')

@section('pageTitleMain',htmlspecialchars($blog['title']))
@section('pageKeywords',htmlspecialchars($blog['seoKeywords']))
@section('pageDescription',htmlspecialchars($blog['seoDescription']))

@section('bodyContent')

    <div class="blog">
        <div class="head">
            <h1>
                <a href="javascript:;" onclick="window.history.back();"><i class="uk-icon-angle-left"></i></a>
                {{$blog['title']}}
            </h1>
            <div class="attr">
                <time datetime="{{$blog['postTime']}}"></time>发布，{{$blog['clickCount'] or 0}} 人读过
            </div>
            @if(!empty($blog['tag']))
                <div class="tag">
                    @foreach($blog['tag'] as $tag)
                        <span>{{$tag}}</span>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="body">
            @if(!empty($blog['summary']))
                <div class="summary">
                    {!! \TechSoft\Laravel\Util\HtmlUtil::text2html($blog['summary']) !!}
                </div>
            @endif
            @if(!empty($blog['images']))
                <div class="images">
                    @foreach($blog['images'] as $image)
                        <div class="image">
                            <img data-src="{{\TechSoft\Laravel\Assets\AssetsUtil::fix($image)}}" />
                        </div>
                    @endforeach
                </div>
            @endif
            @if(!empty($blog['content']))
                <div class="content">
                    {!! \TechSoft\Laravel\Util\HtmlUtil::replaceImageSrcToLazyLoad($blog['content'],'data-src',true) !!}
                </div>
            @endif
            <div class="share">
                <div data-share-buttons data-sites="weibo,qq,qzone,wechat"></div>
            </div>
        </div>
        <div class="nav">
            <div class="uk-grid">
                <div class="uk-width-1-2 uk-text-left">
                    @if($prevBlog)
                        <a href="/blog/{{$prevBlog['id']}}">上一篇：{{$prevBlog['title']}}</a>
                    @endif
                </div>
                <div class="uk-width-1-2 uk-text-right">
                    @if($nextBlog)
                        <a href="/blog/{{$nextBlog['id']}}">下一篇：{{$nextBlog['title']}}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(\TechSoft\Laravel\Config\ConfigUtil::get('blogCommentEnable',false))

        <div class="comment page-block">
            <form action="/blog/{{$blog['id']}}/comment" method="post" data-ajax-form>
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
                            <img src="/blog/comment_captcha?{{time()}}" data-captcha onclick="this.src='/blog/comment_captcha?'+Math.random();" style="height:48px;border:1px solid #DDD;border-radius:3px;width:100%;" />
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
            @foreach($comments as $comment)
                <section class="item page-block">
                    <div class="head">
                        <div class="avatar">
                            <img src="@assets('assets/lib/img/avatar.jpg')" />
                        </div>
                        @if(empty($comment['username']))
                            <h3 class="uk-text-muted">匿名</h3>
                        @else
                            <h3>
                                @if($comment['url'])
                                    <a href="{{$comment['url']}}" target="_blank">{{$comment['username']}}</a>
                                @else
                                    {{$comment['username']}}
                                @endif
                            </h3>
                        @endif
                        <h4><time datetime="{{$comment['created_at']}}"></time>说：</h4>
                    </div>
                    <div class="content">
                        {!! \TechSoft\Laravel\Util\HtmlUtil::text2html($comment['content']) !!}
                    </div>
                </section>
            @endforeach
        </div>

        <div class="page-container">
            {!! $commentPageHtml !!}
        </div>

    @endif

@endsection
