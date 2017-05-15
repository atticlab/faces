<?php

namespace App\Lib;

class Helpers
{
    public static function sfile($str, $rootPath = null, $depth = 2)
    {
        if (!empty($rootPath)) {
            $rootPath = rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        }

        $pattern = '/[^a-z0-9]/';
        $s = preg_replace($pattern, '', strtolower($str));
        $path = '';

        if (strlen($s) >= $depth) {
            for ($i = 1; $i <= $depth; $i++) {
                $path .= substr($s, 0, $i) . '/';
            }
        }

        if (!empty($rootPath) && !is_dir($rootPath . $path)) {
            mkdir($rootPath . $path, 0775, true);
        }

        return $rootPath . $path;
    }
}