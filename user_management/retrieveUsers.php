<?php
require_once '../lib/lib_project1.php';

redirectIfLoggedOut();

$result = array();

foreach ( $MEMBER_DB_MANAGER->getUsers() as $username => $user )
{
    $result[$username]['first_name'] = $user->getFirstName();
    $result[$username]['last_name'] = $user->getLastName();
    $result[$username]['email'] = $user->getEmail();
    $result[$username]['enabled'] = $user->isEnabled()? 'true':'false';
    $result[$username]['date_joined'] = $user->getDate_joined();
    $result[$username]['last_online'] = $user->getLast_online();
    $result[$username]['authorities'] = array();

    foreach ( $user->getAuthorities() as $auth )
    {
        $result[$username]['authorities'][] = $auth;
    }
}

echo json_encode( $result );

header('Content-Type: application/json');
?>