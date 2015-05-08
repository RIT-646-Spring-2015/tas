<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

// This is a secure area!
if ( !$TAS_DB_MANAGER->isAdmin() )
{
    redirectIfLoggedIn();
}
?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Course Management", array ( '/css/managementStyle.css' ), 
        array ( '/js/lib/jquery.tablesorter.js', '/js/lib/underscore-min.js', 
                        '/js/CourseManagementWidget.js' ) );
?>

<body>
    <?= templateHeader( true, true, true, false, true )?>
    <div id="content"></div>
</body>

</html>