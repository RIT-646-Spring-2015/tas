<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$username = $_POST['username'];

$user = $TAS_DB_MANAGER->loadUserByUsername( $username );

    $result['username'] = $user->getUsername();
    $result['firstName'] = $user->getFirstName();
    $result['lastName'] = $user->getLastName();
    $result['email'] = $user->getEmail();
    $result['enabled'] = $user->isEnabled()? 'true':'false';
    $result['dateJoined'] = $user->getDate_joined();
    $result['lastOnline'] = $user->getLast_online();
    $result['authorities'] = array();

    foreach ( $user->getAuthorities() as $auth )
    {
        $result['authorities'][] = $auth;
    }

header('Content-Type: application/json');
echo json_encode( $result );
?>
