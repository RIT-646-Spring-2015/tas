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
    $result['enrolled'][$role][$username]['fullName'] = sprintf( '(%s %s)', $user->getFirstName(), 
            $user->getLastName() );
    $result['enrolled'][$role][$username]['email'] = htmlentities( 
            sprintf( '<%s>', $user->getEmail() ) );
}

foreach ( $course->getTopics() as $topicName => $username )
{
    $topic = $TAS_DB_MANAGER->loadTopicByName( $topicName );
    $user = $TAS_DB_MANAGER->loadUserByUsername( $username );
    $result['topics'][$username]['username'] = sprintf( '%s (%s %s)', $username, $user->getFirstName(), 
            $user->getLastName() );
    $result['topics'][$username]['topic'] = $topicName;
    $result['topics'][$username]['status'] = $topic->getStatus();
}

header( 'Content-Type: application/json' );
echo json_encode( $result );
?>
