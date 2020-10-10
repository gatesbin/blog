@extends('admin::frameDialog')

@section('pageTitle',$config['pageTitleView'])

@section('dialogBody')

    <div class="admin-form">
        <table>
            <tbody>
            @foreach($runtimeData['fields'] as $key)
                <?php $field = &$config['fields'][$key]; ?>
                <tr>
                    <td>
                        <div class="line">
                            <div class="label">
                                {{$field['title']}}
                            </div>
                            <div class="value">
                                <?php echo isset($data[$key])?$data[$key]:''; ?>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

@endsection