<?php

namespace app\components;

class Icone
{
    private static $fontAwesome;

    public static function fontAwesome()
    {
        if (self::$fontAwesome === null) {
            self::$fontAwesome = require __DIR__ . '/icone-fa.php';
        }

        return self::$fontAwesome;
    }
}
