<?php

$postsTotal = count($data['data']);
$postsPerPage = 5; // hardcoded for now. Later the user may change this number via a select or so
$pages = ceil($postsTotal/$postsPerPage);

// set the page
if (isset($_GET['page'])) {
    $tmp = (int) $_GET['page'];
}
$page = ($tmp >= 1) ? $tmp : 1;
// if the GET parameter is too big, set $page to the last available page
if ($page > $pages) {
    $page = $pages;
}

// build the parts of the url's
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

// disable first and last when the first or last page are selected
$disableFirst = ($page == 1) ? 'disabled' : '';
$disableLast = ($page == $pages) ? 'disabled' : '';

// Set a limit to the number of pagination items
$max = 5; // this is the maximum number of pagination items
$step = floor($max/2); // this is the number of items to the left and to the right of the current item
$firstDisplay = $page - $step; // the first index to display
$lastDisplay = $page + $step; // the last index to display
if ($firstDisplay < 1) {
    $lastDisplay = $max;
    $firstDisplay = 1;
}
if ($lastDisplay > $pages) {
    $firstDisplay = $pages - $max + 1;
    $lastDisplay = $pages;
}

$pagination = '<ul class="pagination justify-content-center">';
$pagination .= '<li class="page-item '.$disableFirst.'"><a class="page-link" href="'.$uri.'?'.$query.'page=1">First</a></li>';
for ($i = $firstDisplay; $i <= $lastDisplay; $i++) {
    $active = ($page == $i) ? 'active' : '';
    $pagination .= '<li class="page-item '.$active.'"><a class="page-link" href="'.$uri.'?'.$query.'page='.$i.'">'.$i.'</a></li>';
}
$pagination .= '<li class="page-item '.$disableLast.'"><a class="page-link" href="'.$uri.'?'.$query.'page='.$pages.'">Last</a></li>';
$pagination .= '</ul>';
