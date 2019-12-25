<?php
namespace App\Services;

use File;

class FileService
{

    /**
     * 保存文件
     * @param $files
     * @param string $path
     * @return bool|\Illuminate\Support\Collection
     */
    public static function saveFile($files, $path = '')
    {
        if (is_null($files) || !count($files)) {

            return false;
        }
        $path = $path ? $path . '/' : $path;
        $destinationPath = 'uploads/' . $path;

        File::isDirectory($destinationPath) or File::makeDirectory($destinationPath, 0777, true, true);

        return collect($files)->map(function ($file) use ($destinationPath, $path) {
            $type = getImageType($file);
            if (!$type) {

                return [
                    'status'  => 'FAIL',
                    'message' => '文件类型错误',
                ];
            }

            $filename = time() . rand(100000, 999999) . $type;
            $file->move($destinationPath, $filename);

            return [
                'status' => 'SUCCESS',
                'path'   => $path.$filename,
            ];
        });
    }

}
