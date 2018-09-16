<?php


namespace Verse\Run\Util;


class RestMethodHelper
{
    public static function makeStrictParams(&$params)
    {
        if (!\is_array($params)) {
            return $params;
        }
        
        foreach ($params as &$value) {
            if (\is_array($value)) {
                self::makeStrictParams($value);
            } elseif (is_numeric($value) && (string)(int)$value === $value) {
                $value = (int)$value;
            }
        }
        
        return $params;
    }
}
