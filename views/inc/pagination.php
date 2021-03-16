<?php

$postsTotal = count($data['data']);
$postsPerPage = 5; // hardcoded for now. Later the user may change this number via a select or so
$pages = ceil($postsTotal/$postsPerPage);

$pagination = '<ul class="pagination justify-content-center">';
$pagination .= '<li class="page-item"><a class="page-link" href="/index.php?page=1">First</a></li>';
for ($i = 1; $i <= $pages; $i++) {
    $pagination .= '<li class="page-item"><a class="page-link" href="/index.php?page='.$i.'">'.$i.'</a></li>';
}
$pagination .= '<li class="page-item"><a class="page-link" href="/index.php?page='.$pages.'">Last</a></li>';
$pagination .= '</ul>';
