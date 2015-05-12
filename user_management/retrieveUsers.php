<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();
$courseEnrollments = isset( $_POST['notInCourse'] ) ? $TAS_DB_MANAGER->loadCourseByNumber( 
        $_POST['notInCourse'] )->getEnrolled() : array ();

$result = array ();

foreach ( $TAS_DB_MANAGER->getUsers() as $username => $user )
{
    if ( array_key_exists( $username, $courseEnrollments ) )
        continue;
    
    $result[$username]['firstName'] = $user->getFirstName();
    $result[$username]['lastName'] = $user->getLastName();
    $result[$username]['email'] = $user->getEmail();
    $result[$username]['enabled'] = $user->isEnabled() ? 'true' : 'false';
    $result[$username]['dateJoined'] = $user->getDate_joined();
    $result[$username]['lastOnline'] = $user->getLast_online();
    $result[$username]['authorities'] = array ();
    
    foreach ( $user->getAuthorities() as $auth )
    {
        $result[$username]['authorities'][] = $auth;
    }
}

echo json_encode( $result );

header( 'Content-Type: application/json' );
?>