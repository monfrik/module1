<?php
class Image{
    public static function saveImage()
    {
        if (intval($_FILES['image']['size']) > 2100000) return false;
        $tmp = $_FILES['image']["tmp_name"];
        $realName = $_FILES['image']["name"];
        $nameFile = time().'_'.$realName;
        $allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
        if (is_uploaded_file($tmp)) {
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileMimeType = finfo_file($fileInfo, $tmp);
            if (in_array($fileMimeType, $allowedTypes)){
                $srcCurrent = '../user/posts/'.$nameFile;
            }
            move_uploaded_file($tmp, $srcCurrent);
            finfo_close($fileInfo);
        } else return false;
        return $srcCurrent;
    }
}