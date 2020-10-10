<?php

namespace TechOnline\Utils\Storage;

interface Storage
{

    public function mapListGroupData($path);

    public function mapListGroups($path);

    public function mapPut($group, $key, $value);

    public function mapGet($group, $key);

    public function mapHas($group, $key);

    public function exists($path);

    public function put($path, $data);

    public function get($path);

    public function getOrCreateFromResponse($path, $callback);

    public function listPattern($path);
}