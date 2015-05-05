<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$username = $_POST['username'];

echo $username;

$TAS_DB_MANAGER->deleteUser( $username );

?>