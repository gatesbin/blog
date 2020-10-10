<?php

namespace TechSoft\Laravel\Data;

use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\Response;
use TechOnline\Laravel\Util\TreeUtil;
use TechOnline\Utils\FileUtil;
use TechOnline\Utils\ImageUtil;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use TechSoft\Laravel\Admin\Util\AdminPowerUtil;

class SelectDialogHandle
{


    public function executeCustom($category, $uploadTable, $uploadCategoryTable, $userId, $option = [], $view = 'soft::data.selectDialog')
    {
        if (Request::isMethod('post')) {
            switch (Input::get('action')) {

                case 'simple_list':
                    $list = [];
                    $page = intval(Input::get('page', 1));
                    if ($page < 1) {
                        $page = 1;
                    }
                    $pageSize = 20;
                    $option = [];
                    $option['order'] = ['id', 'desc'];
                    $option['where'] = ['userId' => $userId, 'category' => $category];
                    $paginateData = ModelUtil::paginate($uploadTable, $page, $pageSize, $option);
                    ModelUtil::join($paginateData['records'], 'dataId', '_data', 'data', 'id');
                    $list = [];
                    foreach ($paginateData['records'] as $record) {
                        $item = [];
                        $item['id'] = $record['id'];
                        $item['path'] = '/' . DataUtil::DATA . '/' . $record['_data']['category'] . '/' . $record['_data']['path'];
                        if (!empty($record['_data']['domain'])) {
                            $item['path'] = $record['_data']['domain'] . $item['path'];
                        }
                        $item['preview'] = $item['path'];
                        $item['filename'] = htmlspecialchars($record['_data']['filename']);
                        $item['type'] = FileUtil::extension($record['_data']['path']);
                        $item['category'] = $category;
                        $list[] = $item;
                    }
                    $data = [];
                    $data['total'] = $paginateData['total'];
                    $data['list'] = $list;
                    $data['page'] = $page;
                    $data['pageSize'] = $pageSize;
                    return Response::json(0, null, $data);

                case 'upload_file':
                    $file = Input::file('file');
                    if (empty($file)) {
                        return Response::json(-1, '上传文件为空');
                    }
                    $input = [
                        'file' => $file,
                        'name' => $file->getClientOriginalName(),
                        'type' => $file->getClientMimeType(),
                        'lastModifiedDate' => 'no-modified-date',
                        'size' => $file->getClientSize()
                    ];
                    $uploadRet = DataUtil::uploadHandle($category, $input, ['userId' => $userId], $option);
                    if ($uploadRet['code']) {
                        return Response::json(-1, $uploadRet['msg']);
                    }
                    $ret = DataUtil::storeTempDataByPath($uploadRet['data']['path'], $option);
                    if ($ret['code']) {
                        return Response::json(-1, $ret['msg']);
                    }
                    $categoryId = intval(Input::get('categoryId'));
                    if ($categoryId <= 0) {
                        $categoryId = 0;
                    }
                    $path = $ret['data']['path'];
                    $data = $ret['data']['data'];
                    ModelUtil::insert($uploadTable,
                        [
                            'userId' => $userId,
                            'category' => $data['category'],
                            'dataId' => $data['id'],
                            'uploadCategoryId' => $categoryId,
                        ]
                    );
                    return Response::json(0, null, [
                        'name' => $data['filename'],
                        'path' => join('/', [!empty($data['domain']) ? $data['domain'] : '', $path]),
                    ]);

                case 'upload_raw':
                    $data = Input::get('data');
                    if (empty ($data)) {
                        return Response::json(-1, 'data empty');
                    }
                    $data = preg_replace('/^data:image\\/png;base64,/i', '', $data);
                    $img = @base64_decode($data);
                    if (empty($img)) {
                        return Response::json(-1, 'img empty');
                    }
                    $ret = DataUtil::upload($category, date('Ymd_His') . '.png', $img);
                    if ($ret['code']) {
                        return Response::json(-1, 'error:' . $ret['msg']);
                    }
                    $data = $ret['data']['data'];
                    $path = $ret['data']['path'];
                    $fullPath = $ret['data']['fullPath'];
                    ModelUtil::insert($uploadTable, ['userId' => $userId, 'category' => $data['category'], 'uploadCategoryId' => 0, 'dataId' => $data['id']]);
                    $retData = [];
                    $retData['name'] = $data['filename'];
                    $retData['path'] = $fullPath;
                    return Response::json(0, null, $retData);

                case 'categoryDelete':

                    $id = intval(Input::get('id'));
                    $category = ModelUtil::get($uploadCategoryTable, ['id' => $id, 'userId' => $userId,]);
                    if (empty($category)) {
                        return Response::json(-1, '分类不存在');
                    }

                    $uploadCategories = ModelUtil::all($uploadCategoryTable, ['userId' => $userId,]);
                    $childIds = TreeUtil::allChildIds($uploadCategories, $id);
                    $childIds[] = $id;

                    foreach ($childIds as $childId) {
                        ModelUtil::update($uploadTable, ['userId' => $userId, 'uploadCategoryId' => $childId], ['uploadCategoryId' => 0]);
                    }
                    foreach ($childIds as $childId) {
                        ModelUtil::delete($uploadCategoryTable, ['userId' => $userId, 'id' => $childId]);
                    }

                    return Response::json(0, null);

                case 'categoryEdit':

                    $id = intval(Input::get('id'));
                    $pid = intval(Input::get('pid'));
                    $title = trim(Input::get('title'));

                    if (empty($title)) {
                        return Response::json(-1, '名称为空');
                    }

                    if ($id) {
                        $category = ModelUtil::get($uploadCategoryTable, ['id' => $id, 'userId' => $userId,]);
                        if (empty($category)) {
                            return Response::json(-1, '分类不存在');
                        }
                        if (!TreeUtil::modelNodeChangeAble($uploadCategoryTable, $id, $category['pid'], $pid)) {
                            return Response::json(-1, '分类父分类不能这样修改');
                        }
                        ModelUtil::update($uploadCategoryTable, ['id' => $id, 'userId' => $userId,], [
                            'pid' => $pid,
                            'sort' => null,
                            'title' => $title,
                        ]);
                    } else {
                        ModelUtil::insert($uploadCategoryTable, [
                            'userId' => $userId,
                            'category' => $category,
                            'pid' => $pid,
                            'sort' => null,
                            'title' => $title,
                        ]);
                    }

                    return Response::json(0, null);

                case 'category':

                    $uploadCategories = ModelUtil::all($uploadCategoryTable, ['userId' => $userId, 'category' => $category]);
                    $categories = [];
                    foreach ($uploadCategories as $uploadCategory) {
                        $categories[] = [
                            'name' => $uploadCategory['title'],
                            'id' => $uploadCategory['id'],
                            'pid' => $uploadCategory['pid'],
                            'sort' => $uploadCategory['sort'],
                        ];
                    }
                    TreeUtil::setChildKey('children');
                    $categoryTree = TreeUtil::nodeMerge($categories);

                    $categoryTreeParent = [
                        [
                            'name' => '已归类',
                            'children' => $categoryTree,
                            'id' => 0,
                        ],
                    ];

                    $categoryTreeAll = [
                        [
                            'name' => '已归类',
                            'children' => $categoryTree,
                            'id' => 0,
                        ],
                        [
                            'name' => '未归类',
                            'children' => [],
                            'id' => -1,
                        ]
                    ];

                    $categoryListParent = TreeUtil::listIndent($categoryTreeParent, 'id', 'name');
                    $categoryListAll = TreeUtil::listIndent($categoryTreeAll, 'id', 'name');

                    return Response::json(0, null, compact('categoryTreeParent', 'categoryListParent', 'categoryTreeAll', 'categoryListAll', 'categories'));

                case 'fileDelete':
                case 'imageDelete':

                    $ids = [];
                    foreach (explode(',', trim(Input::get('id', ''))) as $id) {
                        $id = intval($id);
                        if (empty($id)) {
                            continue;
                        }
                        $ids[] = $id;
                    }

                    foreach ($ids as $id) {
                        $adminUpload = ModelUtil::get($uploadTable, ['id' => $id, 'userId' => $userId,]);
                        if (empty($adminUpload)) {
                            continue;
                        }
                        DataUtil::deleteById($adminUpload['dataId'], $option);
                        ModelUtil::delete($uploadTable, ['id' => $id, 'userId' => $userId,]);
                    }

                    return Response::json(0, null);

                case 'fileEdit':
                case 'imageEdit':

                    $ids = [];
                    foreach (explode(',', trim(Input::get('id', ''))) as $id) {
                        $id = intval($id);
                        if (empty($id)) {
                            continue;
                        }
                        $ids[] = $id;
                    }

                    $categoryId = intval(Input::get('categoryId'));

                    foreach ($ids as $id) {
                        ModelUtil::update($uploadTable, ['id' => $id, 'userId' => $userId,], ['uploadCategoryId' => $categoryId]);
                    }

                    return Response::json(0, null);

                case 'list':

                    $page = intval(Input::get('page', 1));
                    if ($page < 1) {
                        $page = 1;
                    }
                    $pageSize = 20;
                    $option = [];
                    $option['order'] = ['id', 'desc'];
                    $option['where'] = ['userId' => $userId, 'category' => $category];

                    $categoryId = intval(Input::get('categoryId'));
                    if ($categoryId > 0) {
                        $uploadCategories = ModelUtil::all($uploadCategoryTable, ['userId' => $userId,]);
                        $childIds = TreeUtil::allChildIds($uploadCategories, $categoryId);
                        $childIds[] = $categoryId;
                        $option['whereIn'] = ['uploadCategoryId', $childIds];
                    } else if ($categoryId == 0) {
                        $option['whereOperate'] = ['uploadCategoryId', '>', 0];
                    } else if ($categoryId == -1) {
                        $option['where']['uploadCategoryId'] = 0;
                    }

                    $paginateData = ModelUtil::paginate($uploadTable, $page, $pageSize, $option);
                    ModelUtil::join($paginateData['records'], 'dataId', '_data', 'data', 'id');

                    $list = [];
                    foreach ($paginateData['records'] as $record) {
                        $item = [];
                        $item['id'] = $record['id'];
                        $item['path'] = '/' . DataUtil::DATA . '/' . $record['_data']['category'] . '/' . $record['_data']['path'];
                        if (!empty($record['_data']['domain'])) {
                            $item['path'] = $record['_data']['domain'] . $item['path'];
                        }
                        $item['preview'] = $item['path'];
                        $item['filename'] = htmlspecialchars($record['_data']['filename']);
                        $item['type'] = FileUtil::extension($record['_data']['path']);
                        $item['category'] = $category;
                        $list[] = $item;
                    }

                    $data = [];
                    $data['total'] = $paginateData['total'];
                    $data['list'] = $list;
                    $data['page'] = $page;
                    $data['pageSize'] = $pageSize;
                    return Response::json(0, null, $data);

                case 'init':
                    return DataUtil::uploadHandle($category, Input::all(), ['userId' => $userId], $option);

                case 'save':

                    $path = Input::get('path');
                    if (empty($path)) {
                        return Response::json(-1, 'path empty');
                    }

                    $func = config('data.upload.' . $category . '.formatChecker');
                    if ($func) {
                        $fullPath = DataUtil::getTempFullPath($path);
                        $pcs = explode('::', $func);
                        if (count($pcs) == 1) {
                            $ret = $func($fullPath);
                        } else if (count($pcs) == 2) {
                            $cls = $pcs[0];
                            $func = $pcs[1];
                            $ret = $cls::$func($fullPath);
                        } else {
                            throw new \Exception('formatChecker error : ' . $func);
                        }
                        if ($ret['code']) {
                            return Response::json(-1, $ret['msg']);
                        }
                    }

                    $ret = DataUtil::storeTempDataByPath($path, $option);
                    if ($ret['code']) {
                        return Response::json(-1, $ret['msg']);
                    }
                    if ($category == 'image') {
                        if (empty($ret['data']['data']['driver'])) {
                            ImageUtil::limitSizeAndDetectOrientation(
                                $ret['data']['path'],
                                config('data.upload.image.maxWidth', 9999),
                                config('data.upload.image.maxHeight', 9999)
                            );
                        }
                    }

                    $categoryId = intval(Input::get('categoryId'));
                    if ($categoryId <= 0) {
                        $categoryId = 0;
                    }

                    $data = $ret['data']['data'];
                    ModelUtil::insert($uploadTable,
                        [
                            'userId' => $userId,
                            'category' => $data['category'],
                            'dataId' => $data['id'],
                            'uploadCategoryId' => $categoryId,
                        ]
                    );

                    return Response::json(0, null);

                default:
                    return DataUtil::uploadHandle($category, Input::all(), ['userId' => $userId], $option);
            }
        }
        return view($view, [
            'category' => $category,
        ]);
    }

    public function executeForAdmin($category, $option = [])
    {
        if (Request::isMethod('post')) {
            switch (Input::get('action')) {
                case 'save':

                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }

                    $path = Input::get('path');
                    if (empty($path)) {
                        return Response::json(-1, 'path empty');
                    }
                    $ret = DataUtil::storeTempDataByPath($path, $option);
                    if ($ret['code']) {
                        return Response::json(-1, $ret['msg']);
                    }

                    if ($category == 'image') {
                        if (file_exists($ret['data']['path'])) {
                            ImageUtil::limitSizeAndDetectOrientation(
                                $ret['data']['path'],
                                config('data.upload.image.maxWidth', 9999),
                                config('data.upload.image.maxHeight', 9999)
                            );
                        }
                    }

                    $categoryId = intval(Input::get('categoryId'));
                    if ($categoryId <= 0) {
                        $categoryId = 0;
                    }

                    $data = $ret['data']['data'];
                    ModelUtil::insert('admin_upload', ['category' => $data['category'], 'dataId' => $data['id'], 'adminUploadCategoryId' => $categoryId,]);

                    return Response::json(0, null);

                case 'categoryDelete':

                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }


                    $id = intval(Input::get('id'));
                    $category = ModelUtil::get('admin_upload_category', ['id' => $id]);
                    if (empty($category)) {
                        return Response::json(-1, '分类不存在');
                    }

                    $adminUploadCategories = ModelUtil::all('admin_upload_category');
                    $childIds = TreeUtil::allChildIds($adminUploadCategories, $id);
                    $childIds[] = $id;

                    foreach ($childIds as $childId) {
                        ModelUtil::update('admin_upload', ['adminUploadCategoryId' => $childId], ['adminUploadCategoryId' => 0]);
                    }
                    foreach ($childIds as $childId) {
                        ModelUtil::delete('admin_upload_category', ['id' => $childId]);
                    }

                    return Response::json(0, null);

                case 'categoryEdit':

                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }

                    $id = intval(Input::get('id'));
                    $pid = intval(Input::get('pid'));
                    $title = trim(Input::get('title'));

                    if (empty($title)) {
                        return Response::json(-1, '名称为空');
                    }

                    if ($id) {
                        $category = ModelUtil::get('admin_upload_category', ['id' => $id]);
                        if (empty($category)) {
                            return Response::json(-1, '分类不存在');
                        }
                        if (!TreeUtil::modelNodeChangeAble('admin_upload_category', $id, $category['pid'], $pid)) {
                            return Response::json(-1, '分类父分类不能这样修改');
                        }
                        ModelUtil::update('admin_upload_category', ['id' => $id], [
                            'pid' => $pid,
                            'sort' => null,
                            'title' => $title,
                        ]);
                    } else {
                        ModelUtil::insert('admin_upload_category', [
                            'category' => $category,
                            'pid' => $pid,
                            'sort' => null,
                            'title' => $title,
                        ]);
                    }

                    return Response::json(0, null);

                case 'category':

                    $adminUploadCategories = ModelUtil::all('admin_upload_category', ['category' => $category]);
                    $categories = [];
                    foreach ($adminUploadCategories as $adminUploadCategory) {
                        $categories[] = [
                            'name' => $adminUploadCategory['title'],
                            'spread' => false,
                            'id' => $adminUploadCategory['id'],
                            'pid' => $adminUploadCategory['pid'],
                            'sort' => $adminUploadCategory['sort'],
                            'href' => '#category-' . $adminUploadCategory['id'],
                        ];
                    }
                    TreeUtil::setChildKey('children');
                    $categoryNodes = TreeUtil::nodeMerge($categories);

                    $nodes = [
                        [
                            'name' => '已归类',
                            'spread' => true,
                            'children' => $categoryNodes,
                            'id' => 0,
                            'href' => '#category-0',
                        ],
                        [
                            'name' => '未归类',
                            'spread' => true,
                            'children' => [],
                            'id' => -1,
                            'href' => '#category--1',
                        ]
                    ];

                    $nodeList = TreeUtil::listIndent($nodes, 'id', 'name');

                    return Response::json(0, null, compact('nodes', 'nodeList'));

                case 'imageDelete':

                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }

                    $ids = [];
                    foreach (explode(',', trim(Input::get('id', ''))) as $id) {
                        $id = intval($id);
                        if (empty($id)) {
                            continue;
                        }
                        $ids[] = $id;
                    }

                    foreach ($ids as $id) {
                        $adminUpload = ModelUtil::get('admin_upload', ['id' => $id]);
                        if (empty($adminUpload)) {
                            continue;
                        }
                        DataUtil::deleteById($adminUpload['dataId'], $option);
                        ModelUtil::delete('admin_upload', ['id' => $id]);
                    }

                    return Response::json(0, null);

                case 'imageEdit':

                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }

                    $ids = [];
                    foreach (explode(',', trim(Input::get('id', ''))) as $id) {
                        $id = intval($id);
                        if (empty($id)) {
                            continue;
                        }
                        $ids[] = $id;
                    }

                    $categoryId = intval(Input::get('categoryId'));

                    foreach ($ids as $id) {
                        ModelUtil::update('admin_upload', ['id' => $id], ['adminUploadCategoryId' => $categoryId]);
                    }

                    return Response::json(0, null);

                case 'list':

                    $page = intval(Input::get('page', 1));
                    if ($page < 1) {
                        $page = 1;
                    }
                    $pageSize = 20;
                    $option = [];
                    $option['order'] = ['id', 'desc'];
                    $option['where'] = ['category' => $category];

                    $categoryId = intval(Input::get('categoryId'));
                    if ($categoryId > 0) {
                        $adminUploadCategories = ModelUtil::all('admin_upload_category');
                        $childIds = TreeUtil::allChildIds($adminUploadCategories, $categoryId);
                        $childIds[] = $categoryId;
                        $option['whereIn'] = ['adminUploadCategoryId', $childIds];
                    } else if ($categoryId == 0) {
                        $option['whereOperate'] = ['adminUploadCategoryId', '>', 0];
                    } else if ($categoryId == -1) {
                        $option['where']['adminUploadCategoryId'] = 0;
                    }

                    $paginateData = ModelUtil::paginate('admin_upload', $page, $pageSize, $option);
                    ModelUtil::join($paginateData['records'], 'dataId', '_data', 'data', 'id');

                    $list = [];
                    foreach ($paginateData['records'] as $record) {
                        $item = [];
                        $item['id'] = $record['id'];
                        $item['path'] = '/' . DataUtil::DATA . '/' . $record['_data']['category'] . '/' . $record['_data']['path'];
                        if (!empty($record['_data']['domain'])) {
                            $item['path'] = $record['_data']['domain'] . $item['path'];
                        }
                        $item['filename'] = htmlspecialchars($record['_data']['filename']);
                        $item['type'] = FileUtil::extension($record['_data']['path']);
                        $item['category'] = $category;
                        $list[] = $item;
                    }

                    $data = [];
                    $data['total'] = $paginateData['total'];
                    $data['list'] = $list;
                    $data['pageSize'] = $pageSize;
                    return Response::json(0, null, $data);

                case 'init':
                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }
                    return DataUtil::uploadHandle($category, Input::all(), [], $option);

                default:
                    if (AdminPowerUtil::isDemo()) {
                        return AdminPowerUtil::demoResponse();
                    }
                    return DataUtil::uploadHandle($category, Input::all(), [], $option);
            }
        }
        return view('soft::data.selectDialog', [
            'category' => $category,
        ]);
    }

}
