<?php
require '../lib/lib_tas.php';

redirectIfLoggedOut();

$username = getActingUsername( "You cannot access another user's topic information!" );
if ( isset( $_GET['courseNumber'] ) )
{
    $courseNumber = $_GET['courseNumber'];
    $course = $TAS_DB_MANAGER->loadCourseByNumber( $courseNumber );
    $username = $username == null ? $TAS_DB_MANAGER->getCurrentUser()->getUsername() : $username;
    if ( !array_key_exists( $username, $course->getEnrolled() ) )
    {
        try
        {
            $TAS_DB_MANAGER->failIfNotAdmin( 
                    'You cannot view topics for a course you are not enrolled in!' );
        } catch ( InadequateRightsException $e )
        {
            die( $e->getMessage() );
        }
    }
}
?>

<!DOCTYPE HTML>
<html lang="EN">

<?php
echo templateHead( "Topic Management", 
        array ( '/css/managementStyle.css', '/css/topicManagementStyle.css' ), 
        array ( '/js/lib/jquery.tablesorter.js', '/js/lib/underscore-min.js', 
                        '/js/TopicManagementWidget.js' ) );
?>

<body>

    <?= templateHeader( true, true, true, true, true )?>
    <div id="content"></div>
</body>
<span id="user"><?= isset($_GET['username'])? $username:''?></span>
<span id="courseNumber"><?= isset($_GET['courseNumber'])? $courseNumber:''?></span>
</html>