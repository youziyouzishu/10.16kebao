<?php

use plugin\admin\app\model\Dict;

/**
 * Here is your custom functions.
 */


function getConfig($field,$name)
{
    $data = Dict::get($field);
    return array_reduce($data, function ($carry, $item)use($name) {
        if ($item['name'] === $name) {
            return $item['value'];
        }
        return $carry;
    });
}