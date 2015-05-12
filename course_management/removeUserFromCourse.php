<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

header( 'Content-Type: application/json' );

$username = $_POST['username'];
$courseNumber = $_POST['courseNumber'];

echo json_encode( $TAS_DB_MANAGER->removeUserFromCourse( $username, $courseNumber ) );
?>
 