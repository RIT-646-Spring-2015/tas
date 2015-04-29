<?php
require_once '../lib/lib_project1.php';

redirectIfLoggedOut();

$username = $_POST['username'];

$user = $MEMBER_DB_MANAGER->loadMemberByUsername( $username );

    $result['username'] = $user->getUsername();
    $result['first_name'] = $user->getFirstName();
    $result['last_name'] = $user->getLastName();
    $result['email'] = $user->getEmail();
    $result['enabled'] = $user->isEnabled()? 'true':'false';
    $result['date_joined'] = $user->getDate_joined();
    $result['last_online'] = $user->getLast_online();
    $result['authorities'] = array();

    foreach ( $user->getAuthorities() as $auth )
    {
        $result['authorities'][] = $auth;
    }

header('Content-Type: application/json');
echo json_encode( $result );
?>
