<?php

use app\lib\Session;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{title}</title>
</head>
<body>
<?php
if (Session::init()->checkLogin()) {
    require_once ROOT.'/views/inc/nav_user.php';
} else {
    require_once ROOT.'/views/inc/nav.php';
}
?>  
{content} 
</body>
</html>