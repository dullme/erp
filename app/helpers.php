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

/**
 * 默认的精度为小数点后两位
 * @param $number
 * @param int $scale
 * @return \Moontoast\Math\BigNumber
 */
function bigNumber($number, $scale = 2)
{
    return new \Moontoast\Math\BigNumber($number, $scale);
}

function getStatusText($status){
    $text = [
        '1' => '中国仓',
        '2' => '海上',
        '3' => '美国仓',
        '4' => '电商',
    ];

    return $text[$status];
}
