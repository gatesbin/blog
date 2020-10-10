<?php

namespace TechSoft\Laravel\Base;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../../views', 'soft');

        $this->publishes([__DIR__ . '/../../config/assets.php' => config_path('assets.php')], 'config');
        $this->publishes([__DIR__ . '/../../config/data.php' => config_path('data.php')], 'config');
    }

    public function register()
    {

        $this->mergeConfigFrom(__DIR__ . '/../../config/assets.php', 'assets');
        $this->mergeConfigFrom(__DIR__ . '/../../config/data.php', 'data');

        $this->app->singleton('assetsPathDriver', config('assets.assets_path_driver'));

        Blade::directive('assets', function ($expression = '') use (&$assetsBase) {
            if (empty($expression)) {
                return '';
            }
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                $file = trim($mat[1], '\'" "');
                return app('assetsPathDriver')->getCDN($file) . app('assetsPathDriver')->getPathWithHash($file);
            } else {
                return '';
            }
        });

        Blade::directive('assetsData', function ($expression = '') {
            if (preg_match('/\\((.+)\\)/i', $expression, $mat)) {
                return "<" . "?php echo empty($mat[1])?" . json_encode(config('assets.assets_image_none', '')) . ":\\TechSoft\\Laravel\\Assets\\AssetsUtil::fix($mat[1]); ?" . ">";
            }
            return "";
        });

    }

}
