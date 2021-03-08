<?php

use app\lib\Session;

if (!empty($data['success'])) {
    include ROOT.'/views/inc/success.php';
}
if (!empty($data['error'])) {
    include ROOT.'/views/inc/error.php';
}

if (Session::init()->checkLogin()) {
    include ROOT.'/views/inc/create.php';
}



//var_dump($data);

echo '<h1>'.$data['title'].'</h1>';

foreach ($data['data'] as $item) {
    $date = (DateTime::createFromFormat('Y-m-d H:i:s', $item['updated_at']))->format('D, j M Y, H:i');
    if ($item['user'] === $_SESSION['user']) {
        // build edit button
        $edit = '<a href="/edit.php?id='.$item['id'].'" class="btn py-0 px-1"><i class="fas fa-pencil-alt"></i></a>';
        // build delete button
        $delete =  '<form action="/delete.php?id='.$item['id'].'" method="POST">';
        $delete .= '<input type="hidden" name="_method" value="delete">';
        $delete .= '<button class="btn py-0 px-1" type="submit"><i class="fas fa-trash-alt"></i></button>';
        $delete .= '</form>';
    } else {
        $edit = '';
        $delete = '';
    }


    echo '<div class="card my-3">';
    echo '<div class="card-header">';
    echo '<div class="d-flex justify-content-between">';
    echo '<strong>'.$item['title'].'</strong>';
    echo '<span class="d-flex align-items-center">by '.$item['user'].
    '<span class="d-flex align-items-center mx-2">'.$edit.$delete.'</span>'.$date.'</span>';
    echo '</div>';
    echo '</div>';
    // pre tags to enable newlines and multiple spaces ... format them later
    echo '<div class="card-body"><pre>'.$item['content'].'</pre></div>';
    // may need that footer later on
    // echo '<div class="card-footer">Footer</div>';
    echo '</div>';
}
