<?php

echo '<h1>Your posts</h1>';

foreach ($data['data'] as $item) {
    $date = (DateTime::createFromFormat('Y-m-d H:i:s', $item['updated_at']))->format('D, j M Y, H:i');
    echo '<div class="card my-3">';
    echo '<div class="card-header">';
    echo '<div class="d-flex justify-content-between">';
    echo '<strong>'.$item['title'].'</strong>';
    echo '<span>'.$date.'</span>';
    echo '</div>';
    echo '</div>';
    // pre tags to enable newlines and multiple spaces ... format them later
    echo '<div class="card-body"><pre>'.$item['content'].'</pre></div>';
    // may need that footer later on
    // echo '<div class="card-footer">Footer</div>';
    echo '</div>';
}
