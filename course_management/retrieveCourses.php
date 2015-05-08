<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$result = array ();

foreach ( $TAS_DB_MANAGER->getCourses() as $courseNumber => $course )
{
    $result[$courseNumber]['name'] = $course->getName();
    
    foreach ( $TAS_DB_MANAGER->getAvailableAuthorities() as $auth )
    {
        if ( $auth != $TAS_DB_MANAGER::AUTHORITY_ADMIN )
            $result[$courseNumber]['enrolled'][$auth] = array ();
    }
    
    foreach ( $course->getEnrolled() as $username )
    {
        $user = $TAS_DB_MANAGER->loadUserByUsername( $username );
        foreach ( $user->getAuthorities() as $auth )
        {
            $result[$courseNumber]['enrolled'][$auth][] = $username;
        }
    }
}

echo json_encode( $result );

header( 'Content-Type: application/json' );
?>