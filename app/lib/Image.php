<?php

namespace app\lib;

class Image
{
    protected $type;
    protected $src;
    protected $srcW;
    protected $srcH;
    protected $dest;
    protected $destW;
    protected $destH;

    public function __construct($src)
    {
        $info = getimagesize($src);
        $this->srcW = $info[0];
        $this->srcH = $info[1];
        switch ($info[2]) {
            case 1: $this->type = 'gif'; break;
            case 2: $this->type = 'jpg'; break;
            case 3: $this->type = 'png'; break;
        }
        $this->src = $this->createImage($src);
    }

    /**
     * Creates an image out of the given source
     * @param mixed $filename
     * @return bool
     */
    protected function createImage($filename)
    {
        switch ($this->type) {
            case 'gif': return imagecreatefromgif($filename);
            case 'jpg': return imagecreatefromjpeg($filename);
            case 'png':
                $transparent = imagecreatefrompng($filename);
                imagealphablending($transparent, true);
                imagesavealpha($transparent, true);
                return $transparent;
        }
        return false;
    }

    /**
     * Prevents upsizing
     */
    protected function preventUpsize()
    {
        if ($this->destW >= $this->srcW || $this->destH >= $this->srcH) {
            $this->destW = $this->srcW;
            $this->destH = $this->srcH;
        }
    }

    /**
     * Resize the whole image
     * @param int $size The new size
     * @param bool $resizeY [default false] Resize based on the height of the image
     * @param bool $preventUpsize [default true]
     * @return $this
     */
    public function resize(int $size, $resizeY=false, $preventUpsize=true)
    {
        if ($resizeY) {
            $this->destH = $size;
            $this->destW = $this->srcW * ($size/$this->srcH);
        } else {
            $this->destW = $size;
            $this->destH = $this->srcH * ($size/$this->srcW);
        }
        if ($preventUpsize) {
            $this->preventUpsize();
        }
        $this->dest = imagecreatetruecolor($this->destW, $this->destH);
        imagecopyresized($this->dest, $this->src, 0, 0, 0, 0, $this->destW, $this->destH, $this->srcW, $this->srcH);
        return $this;
    }

    /**
     * Make the image a square of the given size
     * @param int $size The new size
     * @param bool $preventUpsize [default true]
     * @return $this
     */
    public function square(int $size, $preventUpsize=true)
    {
        // set a square whose sidelength equals the shorter side of the image
        // if landscape
        if ($this->srcW >= $this->srcH) {
            $srcX = $this->srcW/2 - $this->srcH/2;
            $srcY = 0;
            $srcSquare = $this->srcH;
        } else {
            // if portrait
            $srcX = 0;
            $srcY = $this->srcH/2 - $this->srcW/2;
            $srcSquare = $this->srcW;
        }
        $this->destW = $this->destH = $size;
        // prevent upsizing
        if ($preventUpsize) {
            if ($size > $srcSquare) {
                $this->destW = $this->destH = $srcSquare;
            }
        }
        $this->dest = imagecreatetruecolor($this->destW, $this->destH);
        if ($this->type === 'png') {
            imagesavealpha($this->dest, true);
            $trans_colour = imagecolorallocatealpha($this->dest, 0, 0, 0, 127);
            imagefill($this->dest, 0, 0, $trans_colour);
        }
        imagecopyresized($this->dest, $this->src, 0, 0, $srcX, $srcY, $this->destW, $this->destH, $srcSquare, $srcSquare);
        return $this;
    }

    /**
     * Save the image
     * @param mixed $filename
     * @return bool
     */
    public function save($filename)
    {
        switch ($this->type) {
            case 'gif': return imagegif($this->dest ?? $this->src, $filename);
            case 'jpg': return imagejpeg($this->dest ?? $this->src, $filename);
            case 'png': return imagepng($this->dest ?? $this->src, $filename, 0, null);
        }
        return false;
    }
}
