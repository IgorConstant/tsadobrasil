<?php

class Request
{
    public static function getBasePath($absolute = true)
    {
        $root = trim(realpath($_SERVER['DOCUMENT_ROOT']), DIRECTORY_SEPARATOR);
        $current = trim(realpath(dirname($_SERVER['SCRIPT_FILENAME'])), DIRECTORY_SEPARATOR);
        $basePath = str_replace(DIRECTORY_SEPARATOR, '/', trim(str_replace($root, '', $current), DIRECTORY_SEPARATOR));
        return ($absolute ? $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR : '') . $basePath;
    }

    public static function getBaseUrl()
    {
        $basePath = self::getBasePath(false);
        return ($basePath ? '/' . self::getBasePath(false) : '') . '/';
    }

    public static function getRoute()
    {
        if (isset($_GET['r'])) {
            return $_GET['r'];
        } else {
            return str_replace(self::getBaseUrl(), '', $_SERVER['REQUEST_URI']);
        }
    }
}
