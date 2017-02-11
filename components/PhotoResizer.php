<?php

namespace app\components;

use yii\base\Exception;

class PhotoResizer {
    private $src;
    private $copyto;
    private $width;
    private $height;
    private $resizeMode = 1;
    private $q = 90;
    private $rgb = 0xFFFFFF;
    private $aSize;     // = getimagesize($src), вычисляется в конструкторе
    private $backup;
    private $backedUp=false;
    
    public function __construct($src, $copyto=null, $width=0, $height=0, $resizeMode=1, $q=90, $rgb=0xFFFFFF) {
        if (substr($src, 0, 7)!='http://' && !file_exists($src))                         
            throw new Exception('Source file not found. PhotoResizer');
        
        $this->aSize = getimagesize($src);
        if (!$this->aSize)                              throw new Exception('Incorrect file format. PhotoResizer');      
        if(!in_array($this->resizeMode, array(1,2)))    throw new Exception('Incorrect parameter PhotoResizer->resizeMode'); 
        
        $this->src = $src;
        $this->copyto = $copyto;
        $this->width = (int)$width;
        $this->height = (int)$height;
        $this->resizeMode = (int)$resizeMode;
        $this->q = (int)$q;
        $this->rgb = $rgb;
        $this->backup = sys_get_temp_dir() .DIRECTORY_SEPARATOR .uniqid();       // Имя файла для резервного копирования
    }
    
    public function resize() {
        if($this->copyto) {
            $dir = dirname($this->copyto);
            if (!file_exists($dir))
                mkdir($dir, 0777, true);
        }
        
        if($this->copyto && is_file($this->copyto)) {
            rename($this->copyto, $this->backup);
            $this->backedUp = true;
        }

        try {
            if($this->resizeMode==1)        
                return $this->resize1();
            elseif($this->resizeMode==2)    
                return $this->resize2();
            
        } catch (Exception  $e) {
            if($this->backedUp) {
                if(is_file($this->copyto))
                    unlink($this->copyto);
                
                rename($this->backup, $this->copyto);
            }
            
            return false;
        }

    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    ////        http://pers.narod.ru/php/php_pictures_scaling.html
    ////////////////////////////////////////////////////////////////////////////
    // Вариант №1. Скрипт масштабирует рисунок без нарушения пропорций или обрезания сторон так, 
    // чтобы он вписался в предустановленный размер. Так как ширина и высота исходного рисунка 
    // могут быть любыми, по одной из осей может остаться свободное место. 
    private function resize1 () {
        $format = strtolower(substr($this->aSize['mime'], strpos($this->aSize['mime'], '/')+1));
        $icfunc = 'imagecreatefrom'.$format;
        if (!function_exists($icfunc)) return false;

        $x_ratio = $this->width / $this->aSize[0];
        $y_ratio = $this->height / $this->aSize[1];
        $ratio = min($x_ratio, $y_ratio);
        $use_x_ratio = ($x_ratio == $ratio);
        $new_width = $use_x_ratio ? $this->width : floor($this->aSize[0] * $ratio);
        $new_height = !$use_x_ratio ? $this->height : floor($this->aSize[1] * $ratio);
        $new_left = $use_x_ratio ? 0 : floor(($this->width - $new_width) / 2);
        $new_top = !$use_x_ratio ? 0 : floor(($this->height - $new_height) / 2);

        $this->src = $icfunc($this->src);
        $dest = imagecreatetruecolor($this->width, $this->height);
        imagefill($dest, 0, 0, $this->rgb);
        imagecopyresampled($dest, $this->src, $new_left, $new_top, 0, 0,  $new_width, $new_height, $this->aSize[0], $this->aSize[1]);

        // вывод картинки и очистка памяти 
        imagejpeg($dest, $this->copyto, $this->q); 
        imagedestroy($dest); 
        imagedestroy($this->src); 
        return true;
    }    
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Вариант №2 - Скрипт обрезает рисунок так, чтобы соблюдались пропорции целевого рисунка, 
    // а затем масштабирует. При этом некоторая часть изображения может "пропасть", особенно 
    // если пропорции исходного рисунка сильно отличаются от целевых.
     private function resize2() {
        $src_width = $src_copy_width = $this->aSize[0]; 
        $src_height = $src_copy_height = $this->aSize[1]; 
        $src_left = $src_top = 0;
        $dw = $this->width/$src_width; 
        $dh = $this->height/$src_height;

        if ($dw<$dh) {   //обрез.по ширине
            $src_copy_width = round($src_height*$this->width/$this->height);
            $src_left = round(($src_width-$src_copy_width)/2); 
        } else {                                    //обрез.по высоте 
            $src_copy_height = round($src_width*$this->height/$this->width);
            $src_top = round(($src_height-$src_copy_height)/2); 
        }

        $format = strtolower(substr($this->aSize['mime'], strpos($this->aSize['mime'], '/')+1));
        $icfunc = 'imagecreatefrom'.$format;
        if (!function_exists($icfunc)) 
            throw new CHttpException(404,'Не найдена запрашиваемая функция');

        $this->src = $icfunc($this->src);
        $dest = imagecreatetruecolor($this->width, $this->height);
        //imagefill($idest, 0, 0, $rgb);
        imagecopyresampled($dest, $this->src, 0, 0, $src_left, $src_top, $this->width, $this->height, $src_copy_width, $src_copy_height);

        // вывод картинки и очистка памяти 
        imagejpeg($dest, $this->copyto, $this->q); 
        imagedestroy($dest); 
        imagedestroy($this->src); 
        return true;
    }
    
}

?>
