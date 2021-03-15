<?php

namespace app\traits;

use app\lib\Image;

trait Avatar
{
    /**
     * Get an avatar based on filename
     * @param string The filename
     * @return string The outputted image
     */
    public function getAvatar($filename)
    {
        // check if the directory exists
        if (!is_dir(ROOT.'/uploads/avatars')) {
            mkdir(ROOT.'/uploads/avatars', 0777, true);
        }
        // check if an avatar exists
        if (file_exists(ROOT.'/uploads/avatars/'.$filename) && !is_dir(ROOT.'/uploads/avatars/'.$filename)) {
            $imageObj = new Image(ROOT.'/uploads/avatars/'.$filename);
        } else {
            $imageObj = new Image(ROOT.'/uploads/avatars/default.png');
        }
        ob_start();
        $imageObj->square(200)->save(null);
        $avatardefault = ob_get_contents();
        ob_end_clean();
        return $avatardefault;
    }
}
