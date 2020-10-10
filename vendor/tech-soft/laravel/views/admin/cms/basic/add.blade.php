@extends('admin::frameDialog')

@section('pageTitle',$config['pageTitleAdd'])

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
                        <div class="line" data-cms-basic-field="{{$key}}">
                            {!! $html !!}
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </form>
    </div>

@endsection

