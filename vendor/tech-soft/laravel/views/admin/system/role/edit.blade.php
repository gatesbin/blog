@extends('admin::frameDialog')

@if($role)
    @section('pageTitle','编辑角色')
@else
    @section('pageTitle','增加角色')
@endif


@section('dialogBody')

    <form class="admin-form" method="post" action="?" data-ajax-form onsubmit="return false;">
        <table>
            <tbody>
            <tr>
                <td>
                    <div class="line">
                        <div class="label">角色名称</div>
                        <div class="field"><input type="text" name="name" value="{{$role['name'] or ''}}" placeholder="如 新闻编辑" /></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="line">
                        <div class="field">
                            <div class="admin-nested-data">
                                @foreach($powers as $power1)
                                    <div class="item">
                                        <div class="title">
                                            <label>
                                                @if($power1['value'])
                                                    <input type="checkbox" name="rules[]" value="{{$power1['value']}}" @if(!empty($rules[$power1['value']])) checked @endif />
                                                @endif
                                                {{$power1['title']}}
                                            </label>
                                        </div>
                                        @if(!empty($power1['nodes']))
                                            <div class="content">
                                                @foreach($power1['nodes'] as $power2)
                                                    <div class="item">
                                                        <div class="title">
                                                            <label>
                                                                @if($power2['value'])
                                                                    <input type="checkbox" name="rules[]" value="{{$power2['value']}}" @if(!empty($rules[$power2['value']])) checked @endif />
                                                                @endif
                                                                {{$power2['title']}}
                                                            </label>
                                                        </div>
                                                        @if(!empty($power2['nodes']))
                                                            <div class="content">
                                                                @foreach($power2['nodes'] as $power3)
                                                                    <label>
                                                                        @if($power3['value'])
                                                                            <input type="checkbox" name="rules[]" value="{{$power3['value']}}" @if(!empty($rules[$power3['value']])) checked @endif />
                                                                        @endif
                                                                        {{$power3['title']}}
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

@endsection