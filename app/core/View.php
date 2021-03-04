<?php

namespace app\core;

class View
{
    /**
     * Renders a view
     * @param $data Expects an associative array with keys for title and content,
     * where content is the name of the view that is to be rendered. Data, for example
     * a list of posts from the database, is accessed with the key data.
     */
    public function render($data)
    {
        $template = $this->getTemplate();
        $title = $data['title'] ?? 'Messageboard';
        $receivedData = $data['data'] ?? '';
        $content = $this->getView($data['content'], $receivedData);
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

    protected function getView($page, $data)
    {
        ob_start();
        include ROOT.'/views/'.$page;
        return ob_get_clean();
    }
}
