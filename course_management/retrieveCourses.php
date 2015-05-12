<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();
$courseForUser = isset( $_POST['username'] ) ? $TAS_DB_MANAGER->loadUserByUsername( 
        $_POST['username'] ) : null;

$result = array ();

foreach ( $TAS_DB_MANAGER->getCourses() as $courseNumber => $course )
{
    if ( $courseForUser && !array_key_exists( $courseForUser->getUsername(), $course->getEnrolled() ) )
        continue;
    
    $result[$courseNumber]['name'] = $course->getName();
    
    foreach ( $TAS_DB_MANAGER->getAvailableRoles() as $role )
    {
        $result[$courseNumber]['enrolled'][$role] = array ();
    }
    
    foreach ( $course->getEnrolled() as $username => $role )
    {
        $result[$courseNumber]['enrolled'][$role][] = $username;
    }
    
    $result[$courseNumber]['topics'] = array ();
    foreach ( $course->getTopics() as $topicName => $username )
    {
        $result[$courseNumber]['topics'][$topicName] = $username;
    }
}

echo json_encode( $result );

header( 'Content-Type: application/json' );
?>