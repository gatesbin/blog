@extends('admin::frame')

@section('pageTitle','操作日志')

@section('bodyContent')

    <div class="block">

        <div data-admin-lister class="admin-lister-container">

            <div class="lister-search uk-form">
                <div class="item">
                    <div class="label">
                        <b>类型:</b>
                    </div>
                    <div class="field">
                        <select name="type" data-search-field="type" data-search-type="equal">
                            <option value="">全部</option>
                            @foreach(\TechSoft\Laravel\Admin\Type\AdminLogType::getList() as $k=>$v)
                                <option value="{{$k}}">{{$v}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="item">
                    <a class="btn btn-main" href="javascript:;" data-search-button data-uk-tooltip title="搜索"><span class="uk-icon-search"></span></a>
                    <a class="btn btn-default" href="javascript:;" data-reset-search-button data-uk-tooltip title="清空"><span class="uk-icon-refresh"></span></a>
                </div>
            </div>

            <div class="lister-table"></div>
            <div class="page-container"></div>

        </div>

    </div>

@endsection