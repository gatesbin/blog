```
public function mzeditor()
{
    return view('theme.default.m.city.index');
}

public function uploadImage()
{
    $data = [];
    $data['path'] = 'https://cdn.example.com/assets/shang/img/logo_m.png?v=eb6447f9';
    return Response::json(0, null, $data);
}

public function listImages()
{
    $list = [];
    for ($i = Input::get('page') * 12; $i < Input::get('page') * 12 + 12; $i++) {
        $list [] = ['path' => '/placeholder/' . ($i * 10) . 'x' . ($i * 10)];
    }
    $data['list'] = $list;
    return Response::json(0, null, $data);
}
```