@extends('admin::frame')

@section('pageTitle',$config['pageTitle'])

@section('bodyAppend')
    @parent
    {!! $runtimeData['listAppend'] !!}
    {!! $config['bodyAppendHtml'] !!}
@endsection

@section('bodyContent')

    <div class="block admin-form">

        <form action="?" class="uk-form" method="post" data-ajax-form>
            <div style="font-size:13px;">
                <table class="uk-table uk-table-radius uk-table-striped cms-config-form">
                    <tbody>
                    @foreach($config['fields'] as $key=>&$field)
                        <?php $value = isset($data[$key])?$data[$key]:null; ?>
                        <?php $html = $field['_instance']->editHtml($value); ?>
                        @if(!empty($html))
                            <tr data-cms-config-field="{{$key}}">
                                <td>
                                    <div class="line">
                                        {!! $html !!}
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td>
                            <div class="line">
                                <button type="submit" class="uk-button uk-button-primary">保存</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </form>

    </div>

@endsection