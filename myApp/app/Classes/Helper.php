<?php

namespace App\Classes;

class Helper
{
    public static function getContrastColor($hexColor)
    {
        $r = hexdec(substr($hexColor, 1, 2));
        $g = hexdec(substr($hexColor, 3, 2));
        $b = hexdec(substr($hexColor, 5, 2));
        $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
        return ($yiq >= 128) ? 'black' : 'white';
    }

    public static function darkenColor($hexColor)
    {
        $r = hexdec(substr($hexColor, 1, 2)) * 0.8;
        $g = hexdec(substr($hexColor, 3, 2)) * 0.8;
        $b = hexdec(substr($hexColor, 5, 2)) * 0.8;

        return '#'
            . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
