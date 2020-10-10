@extends('admin::frameDialog')

@section('pageTitle',$config['pageTitleView'])

@section('bodyAppend')
    {!! $runtimeData['editAppend'] !!}
@endsection

@section('dialogBody')

    <div class="admin-form">
        <form action="?" class="uk-form" method="post" data-ajax-form onsubmit="return false;">
        <table>
            <tbody>
            @foreach($runtimeData['fields'] as $key)
                <?php $field = &$config['fields'][$key]; ?>
                <?php $value = isset($data[$key])?$data[$key]:null; ?>
                <?php $html = $field['_instance']->editHtml($value); ?>
                <tr>
                    <td>
                        <div class="line" data-cms-category-field="{{$key}}">
                            {!! $html !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
            <input type="hidden" name="_id" value="{{$_id}}" />
        </form>
    </div>

@endsection

