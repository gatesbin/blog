<?php

namespace TechSoft\Laravel\Misc\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use TechOnline\Laravel\Util\FileUtils;


class CrossDomainController extends Controller
{
    public function base64($url)
    {
        $url = base64_decode($url);
        $tempPath = FileUtils::savePathToLocal($url);
        if (file_exists($tempPath)) {
            $content = file_get_contents($tempPath);
            $mine = mime_content_type($tempPath);
            $base64 = base64_encode($content);
        } else {
            $mine = 'data/none';
            $base64 = '';
        }
        $prefix = 'data:' . $mine . ';base64,';
        $body = "if(!('__cross_domain_data' in window)){window.__cross_domain_data={};};window.__cross_domain_data['$url']={prefix:'$prefix',data:'$base64'};";
        return Response::make($body)
            ->header('Content-Type', 'application/javascript')
            ->setSharedMaxAge(30 * 24 * 3600);
    }
}