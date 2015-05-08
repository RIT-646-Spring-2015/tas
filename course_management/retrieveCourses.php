<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$result = array ();

foreach ( $TAS_DB_MANAGER->getCourses() as $courseNumber => $course )
{
    $result[$courseNumber]['name'] = $course->getName();
    
    foreach ( $TAS_DB_MANAGER->getAvailableRoles() as $role )
    {
        $result[$courseNumber]['enrolled'][$role] = array ();
    }
    
    foreach ( $course->getEnrolled() as $username => $role )
    {
        $result[$courseNumber]['enrolled'][$role][] = $username;
    }
    
    $result[$courseNumber]['topics'] = array();
    foreach ( $course->getTopics() as $topicName => $username )
    {
        $result[$courseNumber]['topics'][$topicName] = $username;
    }
}

echo json_encode( $result );

header( 'Content-Type: application/json' );
?>