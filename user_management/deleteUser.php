<?php
require_once '../lib/lib_project1.php';

redirectIfLoggedOut();

$username = $_POST['username'];

echo $username;

$MEMBER_DB_MANAGER->deleteUser( $username );

?>