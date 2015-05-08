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
        $result['enrolled'][$role][] = $username;
    }

header( 'Content-Type: application/json' );
echo json_encode( $result );
?>
