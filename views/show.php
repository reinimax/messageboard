<?php

require_once ROOT.'/views/inc/pagination.php';

echo '<h1>Your posts</h1>';

for ($i = ($page-1)*$postsPerPage; $i < $postsPerPage*$page; $i++) {
    $key = (array_keys($data['data']))[$i];
    $item = $data['data'][$key];
    // break the loop if no more results are found (this prevents the last page from breaking)
    if (!isset($key) || !isset($item)) {
        break;
    }

    $date = (DateTime::createFromFormat('Y-m-d H:i:s', $item[0]['updated_at']))->format('D, j M Y, H:i');
    // build edit button
    $edit = '<a href="/edit.php?id='.$key.'" class="btn py-0 px-1"><i class="fas fa-pencil-alt"></i></a>';
    // build delete button
    $delete =  '<form action="/delete.php?id='.$key.'" method="POST">';
    $delete .= '<input type="hidden" name="_method" value="delete">';
    $delete .= '<button class="btn py-0 px-1" type="submit"><i class="fas fa-trash-alt"></i></button>';
    $delete .= '</form>';

    // build the tags
    $tags = '';
    foreach ($item as $dataset) {
        $tags .= '<span class="badge badge-pill badge-primary mr-1">'.$dataset['tag'].'</span>';
    }

    echo '<div class="card my-3">';
    echo '<div class="card-header">';
    echo '<div class="d-flex justify-content-between">';
    echo '<strong>'.$item[0]['title'].'</strong>';
    echo '<span class="d-flex align-items-center">'.$edit.$delete.$date.'</span>';
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
