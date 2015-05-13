<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$result = array ();

$topicName = $_POST['topicName'];

$topic = $TAS_DB_MANAGER->loadTopicByName( $topicName );

    $result['name'] = $topic->getName();
    $result['username'] = $topic->getSubmittingUsername();
    $result['courseNumber'] = $topic->getCourseNumber();
    $result['link'] = $topic->getLink();
    $result['submissionDate'] = $topic->getSubmissionDate();
    $result['status'] = $topic->getStatus();
    $result['blacklisted'] = $topic->isBlacklisted();

    
header('Content-Type: application/json');
echo json_encode( $result );
?>
