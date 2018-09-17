<?php


namespace Verse\Run\Util;


class Uuid
{
    public static function v4()
    {
        return sprintf('{%04x%04x-%04x-%04x-%04x-%04x%04x%04x}',
            \random_int(0, 65535), \random_int(0, 65535),
            \random_int(0, 65535),
            \random_int(0, 4095) | 0x4000,
            \random_int(0, 0x3fff) | 0x8000,
            \random_int(0, 65535), \random_int(0, 65535), \random_int(0, 65535)
        );
    }
}