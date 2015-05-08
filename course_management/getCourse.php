<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$courseNumber = $_POST['courseNumber'];

$course = $TAS_DB_MANAGER->loadCourseByNumber( $courseNumber );

$result['number'] = $course->getNumber();
$result['name'] = $course->getName();
$result['enrolled'] = array ();

foreach ( $TAS_DB_MANAGER->getAvailableRoles() as $role )
{
    $result['enrolled'][$role] = array ();
}

foreach ( $course->getEnrolled() as $username => $role )
{
    $user = $TAS_DB_MANAGER->loadUserByUsername( $username );
    $result['enrolled'][$role][] = htmlentities( $user->toString() );
}

header( 'Content-Type: application/json' );
echo json_encode( $result );
?>
