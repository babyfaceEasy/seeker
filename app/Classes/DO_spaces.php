<?php


namespace App\Classes;


use App\Constants\Constant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DO_spaces
{
    public static function saveFile($file, $name = '', $type = Constant::PROFILE_PIC_FOLDER)
    {
        $folder = sprintf("%s/%s", Constant::SEAKA_PIC_FOLDER, $type);

        if (!empty($name)) {
            if ($link = Storage::disk('do_spaces')->putFileAs($folder, $file, $name)) {
                return $link;
            }
            return false;
        }

        if ($link = Storage::disk('do_spaces')->putFile( $folder, $file, 'public')){
            return $link;
        }

        return false;
    }

    public static function getFile($name, $type = Constant::PROFILE_PIC_FOLDER)
    {
        $file = Storage::disk('do_spaces')->get($name);
        return $file;
    }

}