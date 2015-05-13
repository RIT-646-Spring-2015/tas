<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();
$topicsForUser = isset( $_POST['username'] ) ? $TAS_DB_MANAGER->loadUserByUsername( 
        $_POST['username'] ) : null;
$topicsForCourse = isset( $_POST['courseNumber'] ) ? $TAS_DB_MANAGER->loadCourseByNumber( 
        $_POST['courseNumber'] ) : null;

$result = array ();

foreach ( $TAS_DB_MANAGER->getTopics() as $topicName => $topic )
{
    if ( $topicsForUser && $topicsForUser->getUsername() != $topic->getSubmittingUsername() )
        continue;
    
    if ( $topicsForCourse && $topicsForCourse->getNumber() != $topic->getCourseNumber() )
        continue;
    
    $result[$topicName]['name'] = $topic->getName();
    $user = $TAS_DB_MANAGER->loadUserByUsername($topic->getSubmittingUsername());
    $result[$topicName]['user']['username'] = $user->getUsername();
    $result[$topicName]['user']['fullName'] = sprintf( '(%s %s)', $user->getFirstName(), $user->getLastName() );
    $result[$topicName]['courseNumber'] = $topic->getCourseNumber();
    $result[$topicName]['link'] = $topic->getLink();
    $result[$topicName]['submissionDate'] = $topic->getSubmissionDate();
    $result[$topicName]['status'] = $topic->getStatus();
    $result[$topicName]['blacklisted'] = $topic->isBlacklisted();
}

echo json_encode( $result );

header( 'Content-Type: application/json' );
?>