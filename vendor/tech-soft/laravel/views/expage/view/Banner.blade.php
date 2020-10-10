@if(!empty($data['image']))
    <div class="ex-page-item">
        <div class="ex-page-item-box ex-page-item-Banner">
            <div class="image">
                <a href="{{$data['url'] or 'javascript:;'}}">
                    <img src="{{\TechSoft\Laravel\Assets\AssetsUtil::fix($data['image'])}}" />
                </a>
            </div>
        </div>
    </div>
@endif