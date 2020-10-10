@extends('admin::frameDialog')

@section('pageTitle','访问IP')

@section('dialogBody')

    <div class="admin-form uk-form">
        <table class="uk-table">
            <thead>
            <tr>
                <th>IP</th>
                <th>访问次数</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ips as $ip=>$cnt)
                <tr>
                    <td>{{$ip}}</td>
                    <td>{{$cnt}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection