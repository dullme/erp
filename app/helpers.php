<?php

/**
 * 获取图片类型
 * @param $image
 * @return bool
 */
function getImageType($image)
{
    $types = [
        1 => '.gif',
        2 => '.jpg',
        3 => '.png',
        4 => '.swf',
        5 => '.psd',
        6 => '.bmp',
    ];

    return isset($types[exif_imagetype($image)]) ? $types[exif_imagetype($image)] : false;
}
