<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$topicName = $_POST['topicName'];

echo $topicName;

$TAS_DB_MANAGER->deleteTopic( $topicName );

?>