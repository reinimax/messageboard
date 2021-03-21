<?php

use app\lib\Session;
use app\traits\Avatar as TraitsAvatar;

require_once ROOT.'/views/inc/pagination.php';

if (!empty($data['success'])) {
    include ROOT.'/views/inc/success.php';
}
if (!empty($data['error'])) {
    include ROOT.'/views/inc/error.php';
}

if (Session::init()->checkLogin()) {
    include ROOT.'/views/inc/create.php';
} else {
    include ROOT.'/views/inc/cta.php';
}

// create a class for making avatars
class Avatar
{
    use TraitsAvatar;
}

/* echo '<pre>';
var_dump($data['data']);
echo '</pre>'; */

echo '<h1>'.$data['title'].'</h1>';

for ($i = ($page-1)*$postsPerPage; $i < $postsPerPage*$page; $i++) {
    $key = (array_keys($data['data']))[$i];
    $item = $data['data'][$key];
    // break the loop if no more results are found (this prevents the last page from breaking)
    if (!isset($key) || !isset($item)) {
        break;
    }

    $date = (DateTime::createFromFormat('Y-m-d H:i:s', $item[0]['updated_at']))->format('D, j M Y, H:i');
    if ($item[0]['user'] === $_SESSION['user']) {
        // build edit button
        $edit = '<a href="/edit.php?id='.$key.'" class="btn py-0 px-1"><i class="fas fa-pencil-alt"></i></a>';
        // build delete button
        $delete =  '<form action="/delete.php?id='.$key.'" method="POST" class="py-0">';
        $delete .= '<input type="hidden" name="_method" value="delete">';
        $delete .= '<button class="btn py-0 px-1" type="submit"><i class="fas fa-trash-alt"></i></button>';
        $delete .= '</form>';
    } else {
        $edit = '';
        $delete = '';
    }

    // build the tags
    $tags = '';
    foreach ($item as $dataset) {
        $tags .= '<span class="badge badge-pill badge-primary mr-1">'.$dataset['tag'].'</span>';
    }
    // create avatar
    $avatar = (new Avatar())->getAvatar($item[0]['avatar']);

    // build username: plain text when logged out, otherwise a link to the profile
    $username = (Session::init()->checkLogin()) ? '<a class="ml-1" href="/user.php?id='.$item[0]['id'].'">'.$item[0]['user'].
    '</a>' : $item[0]['user'];

    echo '<div class="card my-3">';
    echo '<div class="card-header">';
    echo '<img src="data:media_type;base64,'.base64_encode($avatar).
    '" alt="user avatar" class="border rounded-circle shadow-lg float-left mr-2" width="50" height="50">';
    echo '<div class="d-flex justify-content-between">';
    echo '<strong>'.$item[0]['title'].'</strong>';
    echo '<span class="d-flex align-items-center">by '.$username.
    '<span class="d-flex align-items-center mx-2">'.$edit.$delete.'</span>'.$date.'</span>';
    echo '</div>';
    echo '<div class="d-flex">'.$tags.'</div>';
    echo '</div>';
    // pre tags to enable newlines and multiple spaces ... format them later
    echo '<div class="card-body"><pre>'.$item[0]['content'].'</pre></div>';
    // may need that footer later on
    // echo '<div class="card-footer">Footer</div>';
    echo '</div>';
}

echo $pagination;
