<?php


namespace Run\Util;


class HttpParse
{
    const COOKIE_VALUES_COUNT = 2;
    const COOKIE_KEY          = 0;
    const COOKIE_VALUE        = 1;
    const COOKIE_PAIR_DELIMITER = ';'; 
    const COOKIE_VALUE_DELIMITER = '='; 
    
    public static function cookie($cookieHeader)
    {
        $paris = explode(self::COOKIE_PAIR_DELIMITER, $cookieHeader);
        $result = [];
        
        foreach ($paris as $item) {
            $itemData = explode(self::COOKIE_VALUE_DELIMITER, $item, self::COOKIE_VALUES_COUNT);
            if (count($itemData) === 2) {
                $result[trim($itemData[self::COOKIE_KEY])] = trim($itemData[self::COOKIE_VALUE]);    
            }
        }
        
        return $result;
    }
}