<?php

namespace TechSoft\Laravel\Admin\Util;

use Illuminate\Support\Str;
use TechOnline\Laravel\Dao\ModelUtil;
use TechOnline\Laravel\Http\InputPackage;
use TechOnline\Laravel\Type\TypeUtil;
use TechOnline\Utils\ConstantUtil;
use TechSoft\Laravel\Assets\AssetsUtil;

class AdminCmsUtil
{
    public static function thumbViewItem($image)
    {
        $url = AssetsUtil::fixOrDefault($image, 'assets/lib/img/none.png');
        return '<a href="' . $url . '" style="border:1px solid #CCC;display:inline-block;height:42px;width:42px;box-sizing:border-box;border-radius:2px;" data-image-preview><img src="' . $url . '" style="height:40px;width:40px;display:inline-block;" /></a>';
    }

    public static function memberUserId($memberUserId, $controllerView = null)
    {
        $memberUser = ModelUtil::getWithCache('member_user', ['id' => $memberUserId]);
        return self::memberUser($memberUser, $controllerView);
    }

    public static function memberUser($memberUser, $controllerView = null)
    {
        if (null === $controllerView) {
            $controllerView = '\App\Admin\Controller\MemberController@dataView';
        }
        if (!empty($memberUser)) {
            return '<a href="javascript:;" data-dialog-request="' . action($controllerView, ['id' => $memberUser['id']]) . '" class="list-member-user"><img src="'
                . AssetsUtil::fixOrDefault($memberUser['avatar'], 'assets/lib/img/avatar.png') . '" /><span>' . htmlspecialchars($memberUser['username']) . '</span></a>';
        }
        return '';
    }

    public static function userId($userId)
    {
        $user = ModelUtil::getWithCache('user', ['id' => $userId]);
        return self::user($user);
    }

    public static function user($user)
    {
        if (!empty($user)) {
            return '<a href="javascript:;" data-dialog-request="' . action('\App\Admin\Controller\UserController@dataView', ['_id' => $user['id']]) . '" class="list-member-user"><img src="'
                . AssetsUtil::fixOrDefault($user['avatar'], 'assets/lib/img/avatar.png') . '" /><span>' . htmlspecialchars($user['username']) . '</span></a>';
        }
        return '';
    }

    private static function colorGuessMap($typeClass)
    {
        $map = [];
        $guesses = [
            'fail' => 'danger',
            'error' => 'danger',
            'wait_verify' => 'danger',
            'danger' => 'warning',
            'running' => 'warning',
            'success' => 'success',
            'verified' => 'success',
            'reject' => 'warning',
        ];
        foreach (ConstantUtil::dump($typeClass) as $k => $v) {
            $k = strtolower($k);
            foreach ($guesses as $guessKey => $guessValue) {
                if (Str::contains($k, $guessKey)) {
                    $map[$v] = $guessValue;
                    break;
                }
            }

        }
        return $map;
    }

    public static function colorText($typeValue, $typeClass, $colorMap = [])
    {
        if (empty($colorMap)) {
            $colorMap = self::colorGuessMap($typeClass);
        }
        $text = htmlspecialchars(TypeUtil::name($typeClass, $typeValue));
        if (empty($colorMap[$typeValue])) {
            return $text;
        }
        return
            '<span class="uk-text-' . (empty($colorMap[$typeValue]) ? 'default' : $colorMap[$typeValue]) . '">'
            . $text . '</span>';
    }

    public static function colorBadge($typeValue, $typeClass, $colorMap = [])
    {
        if (empty($colorMap)) {
            $colorMap = self::colorGuessMap($typeClass);
        }
        $text = htmlspecialchars(TypeUtil::name($typeClass, $typeValue));
        if (empty($colorMap[$typeValue])) {
            return $text;
        }
        return
            '<span class="uk-badge uk-badge-' . (empty($colorMap[$typeValue]) ? 'default' : $colorMap[$typeValue]) . '">'
            . $text . '</span>';
    }

    public static function retry($link = null)
    {
        return '<a class="btn uk-button-danger" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="重试" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-refresh"></i></a>';
    }

    public static function pass($link = null)
    {
        return '<a class="btn uk-button-success" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="通过" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-check"></i></a>';
    }

    public static function reject($link = null)
    {
        return '<a class="btn uk-button-danger" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="拒绝" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-remove"></i></a>';
    }

    public static function repair($link = null)
    {
        return '<a class="btn uk-button-danger" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="修复" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($link) . '"><i class="uk-icon-wrench"></i></a>';
    }

    public static function button($text, $type = 'primary', $attr = '', $tips = '')
    {
        return '<a ' . $attr . ' class="btn uk-button-' . $type . '" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="' . htmlspecialchars($tips) . '">' . $text . '</a>';
    }

    public static function buttonAjaxRequest($text, $ajaxRequest, $type = 'primary', $attr = '', $tips = '')
    {
        return '<a ' . $attr . ' class="btn uk-button-' . $type . '" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="' . htmlspecialchars($tips) . '" data-ajax-request-loading data-ajax-request="' . htmlspecialchars($ajaxRequest) . '">' . $text . '</a>';
    }

    public static function buttonDialogRequest($text, $ajaxRequest, $type = 'primary', $attr = '', $tips = '')
    {
        return '<a ' . $attr . ' class="btn uk-button-' . $type . '" style="padding:0 0.8em;" href="javascript:;" data-uk-tooltip title="' . htmlspecialchars($tips) . '" data-dialog-request="' . htmlspecialchars($ajaxRequest) . '">' . $text . '</a>';
    }

    public static function dialog($text, $link, $attr = '')
    {
        return '<a href="javascript:;" ' . $attr . ' data-dialog-request="' . htmlspecialchars($link) . '">' . $text . '</a>';
    }

    public static function successText($text)
    {
        return '<span class="uk-text-success">' . $text . '</span>';
    }

    public static function dangerText($text)
    {
        return '<span class="uk-text-danger">' . $text . '</span>';
    }

    public static function warningText($text)
    {
        return '<span class="uk-text-warning">' . $text . '</span>';
    }

    public static function id()
    {
        return InputPackage::buildFromInput()->getInteger('_id');
    }

}
