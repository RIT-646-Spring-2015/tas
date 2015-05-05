<?php
require_once '../lib/lib_tas.php';

redirectIfLoggedOut();

$result = array ();

foreach ( $TAS_DB_MANAGER->getCourses() as $courseNumber => $course )
{
    $result[$courseNumber]['name'] = $course->getName();
    
    foreach ( $TAS_DB_MANAGER->getAvailableAuthorities() as $role )
    {
        if ( $role != $TAS_DB_MANAGER::ROLE_ADMIN )
            $result[$courseNumber]['enrolled'][$role] = array ();
    }
    
    foreach ( $course->getEnrolled() as $username )
    {
        $user = $TAS_DB_MANAGER->loadUserByUsername( $username );
        foreach ( $user->getAuthorities() as $role )
        {
            $result[$courseNumber]['enrolled'][$role][] = $username;
        }
    }
}

echo json_encode( $result );

header( 'Content-Type: application/json' );
?>