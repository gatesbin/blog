@extends('theme.default.pc.frame')

@section('pageTitle',\TechSoft\Laravel\Config\ConfigUtil::get('siteName'))

@section('bodyScript')
    @parent
    <script>
        var list = $('.blog-list').masonry({
            columnWidth:$('.blog-list .blog-list-block')[0],
            itemSelector:'.blog-list-block'
        });
        $('.blog-list').find('img').on('load',function () {
            list.data('masonry').layout();
        });
    </script>
@endsection

@section('bodyContent')

        <div class="blog-list">
            @foreach($blogs as $blog)
                <div class="blog-list-block">
                    <a class="item page-block" href="/blog/{{$blog['id']}}">
                        @if(empty($blog['summary']) && !empty($blog['images'][0]))
                            <div class="cover cover-only">
                                <img data-src="{{\TechSoft\Laravel\Assets\AssetsUtil::fix($blog['images'][0])}}" />
                            </div>
                        @else
                            <h2>{{$blog['title']}}</h2>
                            @if(!empty($blog['images'][0]))
                                <div class="cover">
                                    <img data-src="{{\TechSoft\Laravel\Assets\AssetsUtil::fix($blog['images'][0])}}" />
                                </div>
                            @endif
                            @if(!empty($blog['summary']))
                                <div class="summary">
                                    {!! \TechSoft\Laravel\Util\HtmlUtil::text2html($blog['summary']) !!}
                                </div>
                            @endif
                        @endif
                    </a>
                </div>
            @endforeach
            @if(empty($blogs))
                <div class="empty">
                    暂无记录
                </div>
            @endif
        </div>

        <div class="page-container">
            {!! $pageHtml !!}
        </div>

@endsection