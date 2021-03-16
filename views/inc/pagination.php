<?php

$postsTotal = count($data['data']);
$postsPerPage = 5; // hardcoded for now. Later the user may change this number via a select or so
$pages = ceil($postsTotal/$postsPerPage);

$uri = (explode('?', $_SERVER['REQUEST_URI']))[0];
if ($uri === '/') {
    $uri = 'index.php';
}

if (!empty($_SERVER['QUERY_STRING'])) {
    // cut out previous page from the query-string
    $query = (explode('page', $_SERVER['QUERY_STRING']))[0];
} else {
    $query = '';
}

if ($query !== '' && preg_match('/&$/u', $query) == false) {
    $query .= '&';
}


$pagination = '<ul class="pagination justify-content-center">';
$pagination .= '<li class="page-item"><a class="page-link" href="'.$uri.'?'.$query.'page=1">First</a></li>';
for ($i = 1; $i <= $pages; $i++) {
    $pagination .= '<li class="page-item"><a class="page-link" href="'.$uri.'?'.$query.'page='.$i.'">'.$i.'</a></li>';
}
$pagination .= '<li class="page-item"><a class="page-link" href="'.$uri.'?'.$query.'page='.$pages.'">Last</a></li>';
$pagination .= '</ul>';
