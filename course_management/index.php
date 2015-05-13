<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$username = getActingUsername( "You cannot access another user's course information!" );
?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Course Management", array ( 'css/managementStyle.css' ), 
        array ( 'js/lib/jquery.tablesorter.js', 'js/lib/underscore-min.js', 
                        'js/CourseManagementWidget.js' ) );
?>

<body>
    <?= templateHeader( true, true, true, true, true )?>
    <div id="content"></div>
</body>
<span id="user"><?= isset($_GET['username'])? $username:''?></span>
</html>