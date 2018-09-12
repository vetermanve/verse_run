<?php


namespace Verse\Run\Util;


class Uuid
{
    public static function v4()
    {
        return \Lootils\Uuid\Uuid::createV4();
    }
}