@extends('admin::frameDialog')

@section('pageTitle',$config['pageTitleImport'])

@section('dialogBody')

    <div class="admin-form">
        <form action="?" method="post" data-ajax-form>
            <table>
                <tbody>
                <tr>
                    <td>
                        <div class="line">
                            <div class="field">
                                <div data-upload-button="excelFile" data-category="file"></div>
                                <input type="hidden" name="excelFile" data-upload-button-path="excelFile" />
                                <div class="uk-alert uk-alert-warning uk-text-center" data-upload-button-name="excelFile">[等待上传文件]</div>
                            </div>
                            <div class="help">
                                请按照 <a href="{{action($config['actionImport'])}}?_action=template" target="_blank">模板文件</a> 编辑Excel文件
                            </div>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>

@endsection
