<?php

use app\lib\Session;

if (Session::init()->checkLogin()) {
    include ROOT.'/views/inc/create.php';
}
echo $data;
