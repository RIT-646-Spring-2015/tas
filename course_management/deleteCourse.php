<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$courseNumber = $_POST['courseNumber'];

echo $courseNumber;

$TAS_DB_MANAGER->deleteCourse( $courseNumber );

?>