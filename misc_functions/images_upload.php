<?
namespace UploadImagesScope;

class UploadImages
{
    public function uploadImage($image, $pathToDirectoryStorage, $userPhoto = false)
    {
        global $oDataHandlers;
        
        $allowtype = array('gif', 'jpg', 'jpeg', 'png');
        $fileName = preg_replace('/\s/m', '', $oDataHandlers->stringCleanFromXSS($image['name']));
        $tname = explode(".", strtolower($fileName));
        $type = end($tname);
        
        if(in_array($type, $allowtype)) 
        {
            $imageinfo = getimagesize($image['tmp_name']);
            
            if($imageinfo['mime'] != 'image/gif' && $imageinfo['mime'] != 'image/jpeg' && $imageinfo['mime'] != 'image/jpg' && $imageinfo['mime'] != 'image/png')
            {
                return array(
                    'ERROR' => 'Расширение изображения (' . $type . ') не соответствует заданным типам: gif, jpg, jpeg, png'
                );
            }
            
            $fileNewName = $fileName . time() . '.' . $type;
            
            if(is_uploaded_file($image['tmp_name']))
            {
                $newImage = $this->resizeUploadImage($image['tmp_name'], $imageinfo['mime']);
                
                if($imageinfo['mime'] == 'image/gif' && !imagegif($newImage, $pathToDirectoryStorage . $fileNewName) || $imageinfo['mime'] == 'image/jpeg' && !imagejpeg($newImage, $pathToDirectoryStorage . $fileNewName) || $imageinfo['mime'] == 'image/jpg' && !imagejpeg($newImage, $pathToDirectoryStorage . $fileNewName) || $imageinfo['mime'] == 'image/png' && !imagepng($newImage, $pathToDirectoryStorage . $fileNewName))
                {
                    return array(
                        'ERROR' => 'При загрузке файла возникла ошибка'
                    );
                }
                else
                {
                    $result = array(
                        'FILE_NAME' => $fileNewName
                    );
                    
                    if($userPhoto !== false)
                    {
                        $result['USER_PHOTO'] = true;
                    }
                    
                    return $result;
                }
            }
        }
        else
        {
            return array(
                'ERROR' => 'Расширение изображения (' . $type . ') не соответствует заданным типам: gif, jpg, jpeg, png'
            );
        }
    }
    
    public function resizeUploadImage($file, $type)
    {
        if($type == 'image/gif')
        {
            $image = imagecreatefromgif($file);
        }
        else if($type == 'image/jpeg' || $type == 'image/jpg')
        {
            $image = imagecreatefromjpeg($file);
        }
        else
        {
            $image = imagecreatefrompng($file);
        }
        
        $imageOriginalWidth = imagesx($image);
        $imageOriginalHeight = imagesy($image);
        $imageMaxWidth = 320;
        $imageMaxHeight = 240;
        $ratio = $imageOriginalHeight / $imageMaxHeight;
        $imageMaxWithRatioWidth = round($imageOriginalWidth / $ratio);
        $imageMaxWithRatioHeight = round($imageOriginalHeight / $ratio);
        $x1 = ( $imageMaxWidth / 2 );
        
        if($imageOriginalWidth > $imageMaxWidth || $imageOriginalHeight > $imageMaxHeight)
        {
            $imageCanvas = imagecreatetruecolor($imageMaxWidth, $imageMaxHeight);
            imagecopyresampled($imageCanvas, $image, 0, 0, $x1, 0, $imageMaxWithRatioWidth, $imageMaxWithRatioHeight, $imageOriginalWidth, $imageOriginalHeight);
            imagedestroy($image);
            
            return $imageCanvas;
        }
    }
}
?>