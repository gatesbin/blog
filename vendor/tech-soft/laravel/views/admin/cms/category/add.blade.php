@extends('admin::frameDialog')

@section('pageTitle',$config['pageTitleView'])

@section('bodyAppend')
    {!! $runtimeData['addAppend'] !!}
@endsection

@section('dialogBody')

    <div class="admin-form">
        <form action="?" class="uk-form" method="post" data-ajax-form onsubmit="return false;">
            <table>
                <tbody>
                @foreach($runtimeData['fields'] as $key)
                    <?php $field = &$config['fields'][$key]; ?>
                    <?php $html = $field['_instance']->addHtml(); ?>
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
            <input type="hidden" name="{{$config['parentIdKey']}}" value="{{$_pid}}" />
        </form>
    </div>

@endsection

