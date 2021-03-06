<?php

use app\lib\Session;

if (Session::init()->checkLogin()) {
    include ROOT.'/views/inc/create.php';
}

// var_dump($data);

echo '<h1>The latest posts</h1>';

foreach ($data as $item) {
    $date = (DateTime::createFromFormat('Y-m-d H:i:s', $item['updated_at']))->format('D, j M Y, H:i');
    echo '<div class="card my-3">';
    echo '<div class="card-header">';
    echo '<div class="d-flex justify-content-between">';
    echo '<strong>'.$item['title'].'</strong>';
    echo '<span>by '.$item['user'].'<span class="mx-2"></span>'.$date.'</span>';
    echo '</div>';
    echo '</div>';
    // pre tags to enable newlines and multiple spaces ... format them later
    echo '<div class="card-body"><pre>'.$item['content'].'</pre></div>';
    // may need that footer later on
    // echo '<div class="card-footer">Footer</div>';
    echo '</div>';
}
