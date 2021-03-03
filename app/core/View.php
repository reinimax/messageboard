<?php

namespace app\core;

class View
{
    public function render($data)
    {
        $template = $this->getTemplate();
        $title = $data[0];
        $content = $data[1];
        $output = str_replace(['{title}', '{content}'], [$title, $content], $template);
        echo $output;
    }

    protected function getTemplate()
    {
        // output buffer + include get the file and resolve the php in it before returning it
        // simply using file_get_contents wouldn't work if the file contains any php
        ob_start();
        include ROOT.'/views/template.php';
        return ob_get_clean();
    }
}
