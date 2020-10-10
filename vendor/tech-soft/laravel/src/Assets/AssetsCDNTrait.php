<?php

namespace TechSoft\Laravel\Assets;

use TechSoft\Laravel\Config\ConfigUtil;

trait AssetsCDNTrait
{
    public function bootAssetsCDN()
    {
                try {

                        if (env('URL_CDN')) {
                $this->app->config->set('assets.assets_cdn', env('URL_CDN'));
            } else {
                $cdn = ConfigUtil::get('systemCdnUrl', '/');
                if (empty($cdn)) {
                    $cdn = env('URL_CDN', '/');
                }
                $this->app->config->set('assets.assets_cdn', $cdn);
            }

        } catch (\Exception $e) {
                    }
    }
}
