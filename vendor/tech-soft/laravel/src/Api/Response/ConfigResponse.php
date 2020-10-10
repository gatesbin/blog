<?php

namespace TechSoft\Laravel\Api\Response;


use TechOnline\Utils\FileUtil;
use TechSoft\Laravel\Assets\AssetsUtil;
use TechSoft\Laravel\Config\ConfigUtil;

class ConfigResponse
{
    public static function defaultData()
    {
        $data = [];

        $data['siteLogo'] = AssetsUtil::fixFull(ConfigUtil::get('siteLogo'));
        $data['siteName'] = ConfigUtil::get('siteName');
        $data['siteSlogan'] = ConfigUtil::get('siteSlogan');
        $data['siteDomain'] = ConfigUtil::get('siteDomain');
        $data['siteKeywords'] = ConfigUtil::get('siteKeywords');
        $data['siteDescription'] = ConfigUtil::get('siteDescription');
        $data['siteFavIco'] = AssetsUtil::fixFull(ConfigUtil::get('siteFavIco'));
        $data['siteBeian'] = ConfigUtil::get('siteBeian');

        $data['loginCaptchaEnable'] = ConfigUtil::getBoolean('loginCaptchaEnable');
        $data['registerDisable'] = ConfigUtil::getBoolean('registerDisable');
        $data['registerEmailEnable'] = ConfigUtil::getBoolean('registerEmailEnable');
        $data['registerPhoneEnable'] = ConfigUtil::getBoolean('registerPhoneEnable');
        $data['retrieveDisable'] = ConfigUtil::getBoolean('retrieveDisable');
        $data['retrievePhoneEnable'] = ConfigUtil::getBoolean('retrievePhoneEnable');
        $data['retrieveEmailEnable'] = ConfigUtil::getBoolean('retrieveEmailEnable');

        $data['ssoClientEnable'] = ConfigUtil::getBoolean('ssoClientEnable', false);

        $data['dataUpload'] = [];
        $data['dataUpload'] = [
            'chunkSize' => max(FileUtil::formattedSizeToBytes(ini_get('upload_max_filesize')) - 500 * 1024, 1024),
            'category' => [],
        ];
        $uploads = config('data.upload');
        foreach ($uploads as $category => $categoryInfo) {
            $data['dataUpload']['category'][$category] = [
                'maxSize' => $categoryInfo['maxSize'],
                'extensions' => $categoryInfo['extensions'],
            ];
        }

        return $data;
    }
}
