<?php

namespace TechSoft\Laravel\Assets;


interface AssetsPath
{
    
    public function getPathWithHash($file);

    
    public function getCDN($file);
}

