<?php

$postsTotal = count($data['data']);
$postsPerPage = (isset($_GET['ppp'])) ? ((int) $_GET['ppp']) : 5;
$pages = ceil($postsTotal/$postsPerPage);

// set the page
$tmp = 1;
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
if ($max > $pages) {
    $max = $pages;
}
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

$pagination = '<div class="d-flex justify-content-center align-items-center"><ul class="pagination justify-content-center">';
$pagination .= '<li class="page-item '.$disableFirst.'"><a class="page-link" href="'.$uri.'?'.$query.'page=1">First</a></li>';
for ($i = $firstDisplay; $i <= $lastDisplay; $i++) {
    $active = ($page == $i) ? 'active' : '';
    $pagination .= '<li class="page-item '.$active.'"><a class="page-link" href="'.$uri.'?'.$query.'page='.$i.'">'.$i.'</a></li>';
}
$pagination .= '<li class="page-item '.$disableLast.'"><a class="page-link" href="'.$uri.'?'.$query.'page='.$pages.'">Last</a></li>';
$pagination .= '</ul>';
$pagination .= '<form action="'.$uri.'" method="GET" class="form-inline mb-3 ml-2">';
$pagination .= '<select class="form-control" name="ppp" id="ppp">';
$values = [5,10,15,20,30]; // the values that can be selected for number of posts per page
foreach ($values as $val) {
    $selected = ($postsPerPage == $val) ? 'selected' : '';
    $pagination .= '<option '.$selected.'>'.$val.'</option>';
}
$pagination .= '</select>';
$pagination .= '<label for="ppp" class="mx-2"> posts per page</label>';
// add hidden fields to preserve already existing GET-parameters. For now, this is limited to query and limit from searching
// (page needs not to be preserved as it is calculated anew on fom submission)
if (isset($_GET['query'])) {
    $pagination .= '<input type="hidden" name="query" value="'.htmlspecialchars($_GET['query']).'">';
}
if (isset($_GET['limit'])) {
    $pagination .= '<input type="hidden" name="limit" value="'.htmlspecialchars($_GET['limit']).'">';
}
$pagination .= '<input class="btn btn-success" type="submit" value="Ok">';
$pagination .= '</form></div>';
