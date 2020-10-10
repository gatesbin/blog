@extends('admin::frameDialog')
@section('pageTitle','请选择链接')
@section('dialogBody')

    <style type="text/css">
        .uk-button.uk-active{
            background: #07d!important;
            color: #fff!important;
        }
        #links a{
            display: block;
            border:1px solid #EEE;
            border-radius:3px;
            line-height:30px;
            padding:0 10px;
            margin:0 0 5px 0;
            color:#333;
        }
        #links a span{
            display: block;
            float:right;
            color:#999;
        }
        #links a:hover{
            color:#07d;
            border-color:#07d;
        }
    </style>

    <div style="padding:10px;" data-uk-switcher="{connect:'#links'}">
        @foreach($links as $_=>$link)
            <button class="uk-button {{$_===0?'uk-active':''}}">{{$link['name']}}</button>
        @endforeach
    </div>

    <div style="padding:0 10px 10px 10px;" id="links" class="uk-switcher">
        @foreach($links as $_=>$link)
            <div>
                @foreach($link['items'] as $item)
                    <a href="javascript:;" data-value="{{$item['url']}}">
                        <span>{{$item['url']}}</span>
                        {{$item['name']}}
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>

    <script>
        $(function () {
            $('#links a').on('click',function () {
                window.parent.$("{{Input::get('for')}}").val($(this).attr('data-value'));
                $.dialogClose();
                return false;
            });
        });
    </script>

@endsection
