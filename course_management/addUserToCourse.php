<?php
require_once '../lib/lib_tas.php';

header( 'Content-Type: application/json' );

$username = $_POST['username'];
$courseNumber = $_POST['courseNumber'];
$role = $_POST['role'];

echo json_encode( $TAS_DB_MANAGER->addUserToCourse( $username, $courseNumber, $role ) );
?>
 